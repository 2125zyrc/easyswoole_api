<?php

namespace EasySwoole\Jwt\Tests;

use EasySwoole\Jwt\Jwt;
use EasySwoole\Jwt\JwtObject;
use PHPUnit\Framework\TestCase;

/**
 * Jwt 测试
 * Class JwtTest
 * @package EasySwoole\ORM\Tests
 */
class JwtTest extends TestCase
{
    private $alg;
    private $aud;
    private $exp;
    private $iat;
    private $iss;
    private $jti;
    private $nbf;
    private $sub;
    private $extData;

    protected function setUp()
    {
        parent::setUp();
        $this->alg = Jwt::ALG_METHOD_HMACSHA256;
        $this->aud = 'user';
        $this->exp = time();
        $this->iat = time() + 3600;
        $this->iss = 'admin';
        $this->jti = md5(time());
        $this->nbf = time() + 60 * 5;
        $this->sub = 'auth';
        $this->extData = 'extData';
    }

    public function testJwt()
    {
        $jwtObject = Jwt::getInstance()->publish();
        $this->assertTrue($jwtObject instanceof JwtObject);

        $this->assertTrue($jwtObject->setAlg($this->alg) instanceof JwtObject);
        $this->assertTrue($jwtObject->setAud($this->aud) instanceof JwtObject);
        $this->assertTrue($jwtObject->setExp($this->exp) instanceof JwtObject);
        $this->assertTrue($jwtObject->setIat($this->iat) instanceof JwtObject);
        $this->assertTrue($jwtObject->setIss($this->iss) instanceof JwtObject);
        $this->assertTrue($jwtObject->setJti($this->jti) instanceof JwtObject);
        $this->assertTrue($jwtObject->setNbf($this->nbf) instanceof JwtObject);
        $this->assertTrue($jwtObject->setSub($this->sub) instanceof JwtObject);
        $this->assertTrue($jwtObject->setData($this->extData) instanceof JwtObject);

        $this->assertTrue($jwtObject->getAlg() == $this->alg);
        $this->assertTrue($jwtObject->getAud() == $this->aud);
        $this->assertTrue($jwtObject->getExp() == $this->exp);
        $this->assertTrue($jwtObject->getIat() == $this->iat);
        $this->assertTrue($jwtObject->getIss() == $this->iss);
        $this->assertTrue($jwtObject->getJti() == $this->jti);
        $this->assertTrue($jwtObject->getNbf() == $this->nbf);
        $this->assertTrue($jwtObject->getSub() == $this->sub);
        $this->assertTrue($jwtObject->getData() == $this->extData);

        $this->assertTrue(is_string($jwtObject->__toString()));
    }

    public function testDecode()
    {
        $jwtObject = Jwt::getInstance()->publish();
        $jwtObject->setAlg($this->alg);
        $jwtObject->setAud($this->aud);
        $jwtObject->setExp($this->exp);
        $jwtObject->setIat($this->iat);
        $jwtObject->setIss($this->iss);
        $jwtObject->setJti($this->jti);
        $jwtObject->setNbf($this->nbf);
        $jwtObject->setSub($this->sub);
        $jwtObject->setData($this->extData);
        $token = $jwtObject->__toString();

        $jwtObject = Jwt::getInstance()->decode($token);
        $status = $jwtObject->getStatus();
        $this->assertTrue($status === JwtObject::STATUS_OK);
        $this->assertTrue($jwtObject->getAlg() == $this->alg);
        $this->assertTrue($jwtObject->getAud() == $this->aud);
        $this->assertTrue($jwtObject->getExp() == $this->exp);
        $this->assertTrue($jwtObject->getIat() == $this->iat);
        $this->assertTrue($jwtObject->getIss() == $this->iss);
        $this->assertTrue($jwtObject->getJti() == $this->jti);
        $this->assertTrue($jwtObject->getNbf() == $this->nbf);
        $this->assertTrue($jwtObject->getSub() == $this->sub);
        $this->assertTrue($jwtObject->getData() == $this->extData);

        $jwtObject = Jwt::getInstance()->publish();
        $jwtObject->setExp(time()-3600);
        $status = Jwt::getInstance()->decode($jwtObject->__toString())->getStatus();
        $this->assertTrue($status === JwtObject::STATUS_EXPIRED);

        $jwtObject = Jwt::getInstance()->publish();
        $jwt = $jwtObject->__toString();

        // 把签名解释出来，然修改，然后再放回去
        $raw = json_decode(base64_decode(urldecode($jwt)), true);
        $raw['signature'] = substr_replace($raw['signature'], mt_rand(1000, 9999), -4, 4);
        $jwt = urlencode(base64_encode(json_encode($raw)));

        $status = Jwt::getInstance()->decode($jwt)->getStatus();
        $this->assertTrue($status === JwtObject::STATUS_SIGNATURE_ERROR);
    }

    /**
     * @expectedException \EasySwoole\Jwt\Exception
     */
    public function testException()
    {
        Jwt::getInstance()->decode(mt_rand());
    }
}
