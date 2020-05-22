<?php

namespace EasySwoole\ORM;

use EasySwoole\Component\Singleton;
use EasySwoole\Mysqli\QueryBuilder;
use EasySwoole\ORM\Db\ClientInterface;
use EasySwoole\ORM\Db\ConnectionInterface;
use EasySwoole\ORM\Db\Result;
use EasySwoole\ORM\Exception\Exception;
use EasySwoole\Pool\Exception\PoolEmpty;
use Swoole\Coroutine;

/**
 * Class DbManager
 * @package EasySwoole\ORM
 */
class DbManager
{
    use Singleton;

    protected $connections = [];
    protected $transactionContext = [];
    protected $onQuery;

    public function onQuery(callable $call):DbManager
    {
        $this->onQuery = $call;
        return $this;
    }

    function addConnection(ConnectionInterface $connection,string $connectionName = 'default'):DbManager
    {
        $this->connections[$connectionName] = $connection;
        return $this;
    }

    function getConnection(string $connectionName = 'default'):?ConnectionInterface
    {
        if(isset($this->connections[$connectionName])){
            return $this->connections[$connectionName];
        }
        return null;
    }

    protected function getClient(string $connectionName,float $timeout = null):ClientInterface
    {
        $cid = Coroutine::getCid();
        if(isset($this->transactionContext[$cid][$connectionName])){
            return $this->transactionContext[$cid][$connectionName];
        }
        $connection = $this->getConnection($connectionName);
        if($connection){
            $client = $connection->getClientPool()->getObj($timeout);
            if($client){
                //做名称标记
                $client->__connectionName = $connectionName;
                return $client;
            }else{
                throw new PoolEmpty("connection : {$connectionName} is empty");
            }
        }else{
            throw new Exception("connection : {$connectionName} not register");
        }
    }

    protected function recycleClient(string $connectionName,?ClientInterface $client)
    {
        if(isset($this->transactionContext[$connectionName])){
            return;
        }else if($client){
            $this->getConnection($connectionName)->getClientPool()->recycleObj($client);
        }
    }

    /**
     * @param QueryBuilder $builder
     * @param bool $raw
     * @param string|ClientInterface $connection
     * @param float|null $timeout
     * @return Result
     * @throws Exception
     * @throws \Throwable
     */
    function query(QueryBuilder $builder, bool $raw = false, $connection = 'default', float $timeout = null):Result
    {
        $name = null;
        if(is_string($connection)){
            $name = $connection;
            $client = $this->getClient($connection,$timeout);
        }else if($connection instanceof ClientInterface){
            $client = $connection;
        }else{
            throw new Exception('var $connection not a connectionName or ClientInterface');
        }
        try{
            $start = microtime(true);
            $ret = $client->query($builder,$raw);
            if($this->onQuery){
                $temp = clone $builder;
                call_user_func($this->onQuery,$ret,$temp,$start);
            }
            if(in_array('SQL_CALC_FOUND_ROWS',$builder->getLastQueryOptions())){
                $temp = new QueryBuilder();
                $temp->raw('SELECT FOUND_ROWS() as count');
                $count = $client->query($temp,true);
                if($this->onQuery){
                    call_user_func($this->onQuery,$count,$temp,$start,$client);
                }
                $ret->setTotalCount($count->getResult()[0]['count']);
            }
            return $ret;
        }catch (\Throwable $exception){
            throw $exception;
        } finally {
            if($name){
                $this->recycleClient($name,$client);
            }
        }
    }

    function invoke(callable $call,string $connectionName = 'default',float $timeout = null)
    {
        $client = $this->getClient($connectionName,$timeout);
        if($client){
            try{
                return call_user_func($call,$client);
            }catch (\Throwable $exception){
                throw $exception;
            }finally{
                $this->recycleClient($connectionName,$client);
            }
        }
        return ;
    }

    /**
     * @param string|ClientInterface $con
     * @return bool
     * @throws Exception
     * @throws \Throwable
     */
    public function startTransaction($con = 'default',float $timeout = null):bool
    {
        $defer = true;
        $name = null;
        $client = null;
        if($con instanceof ClientInterface){
            $client = $con;
            $name = $client->__connectionName;
            $defer = false;
        }else{
            $name = $con;
        }
        $cid = Coroutine::getCid();
        //检查上下文
        if(isset($this->transactionContext[$cid][$name])){
            return true;
        }
        if(!$client){
            $client = $this->getClient($name,$timeout);
        }
        try{
            $builder = new QueryBuilder();
            $builder->startTransaction();
            $ret = $this->query($builder,true,$client);
            //外部连接不需要帮忙注册defer清理，需要外部注册者自己做。
            if($ret->getResult() && $defer){
                $this->transactionContext[$cid][$name] = $client;
                Coroutine::defer(function (){
                   $this->transactionDeferExit();
                });
            }
            return $ret->getResult();
        }catch (\Throwable $exception){
            $this->recycleClient($name,$client);
            throw $exception;
        }
    }

    public function commit($con = 'default'):bool
    {
        $outSideClient = false;
        $cid = Coroutine::getCid();
        $name = null;
        $client = null;
        if($con instanceof ClientInterface){
            $client = $con;
            $name = $client->__connectionName;
        }else{
            $name = $con;
            //没有上下文说明没有声明连接事务
            if(!isset($this->transactionContext[$cid][$name])){
                return true;
            }else{
                $client = $this->transactionContext[$cid][$name];
            }
        }
        try{
            $builder = new QueryBuilder();
            $builder->commit();
            $ret = $this->query($builder,true,$client);
            if($ret->getResult()){
                unset($this->transactionContext[$cid][$name]);
                $this->recycleClient($name,$client);
            }
            return $ret->getResult();
        }catch (\Throwable $exception){
            throw $exception;
        }
    }


    public function rollback($con = 'default',float $timeout = null):bool
    {
        $cid = Coroutine::getCid();
        $name = null;
        $client = null;
        if($con instanceof ClientInterface){
            $client = $con;
            $name = $client->__connectionName;
        }else{
            $name = $con;
            //没有上下文说明没有声明连接事务
            if(!isset($this->transactionContext[$cid][$name])){
                return true;
            }else{
                $client = $this->transactionContext[$cid][$name];
            }
        }
        try{
            $builder = new QueryBuilder();
            $builder->rollback();
            $ret = $this->query($builder,true,$client);
            if($ret->getResult()){
                unset($this->transactionContext[$cid][$name]);
                $this->recycleClient($name,$client);
            }
            return $ret->getResult();
        }catch (\Throwable $exception){
            throw $exception;
        }
    }

    protected function transactionDeferExit()
    {

        $cid = Coroutine::getCid();
        if(isset($this->transactionContext[$cid])){
            foreach ($this->transactionContext[$cid] as $con){
                $res = false;
                try{
                    $res = $this->rollback($con);
                }catch (\Throwable $exception){
                    trigger_error($exception->getMessage());
                } finally {
                    if($res){
                        $this->recycleClient($con->__connectionName,$con);
                    }else{
                        //如果这个阶段的回滚还依旧失败，则废弃这个连接
                        $this->getConnection($con->__connectionName)->getClientPool()->unsetObj($con);
                    }
                }
            }
        }
        unset($this->transactionContext[$cid]);
    }
}
