<?php

namespace App\Core;

use Exception;

class JwtHandler
{
  private $secretKey;
  private $algorithm;
  private $expirationTime;

  public function __construct($secretKey = null, $algorithm = 'HS256', $expirationTime = 3600)
  {
    $this->secretKey = $secretKey ?: 'your-secret-key-here-change-this-in-production';
    $this->algorithm = $algorithm;
    $this->expirationTime = $expirationTime; // seconds
  }

  /**
   * Tạo JWT token
   */
  public function generateToken($payload)
  {
    $header = $this->createHeader();
    $payload = $this->createPayload($payload);

    $headerEncoded = $this->base64UrlEncode(json_encode($header));
    $payloadEncoded = $this->base64UrlEncode(json_encode($payload));

    $signature = $this->createSignature($headerEncoded . '.' . $payloadEncoded);

    return $headerEncoded . '.' . $payloadEncoded . '.' . $signature;
  }

  /**
   * Xác thực và giải mã JWT token
   */
  public function validateToken($token)
  {
    if (!$token) {
      throw new Exception('Token is required');
    }

    $parts = explode('.', $token);
    if (count($parts) !== 3) {
      throw new Exception('Invalid token format');
    }

    list($headerEncoded, $payloadEncoded, $signature) = $parts;

    // Verify signature
    $expectedSignature = $this->createSignature($headerEncoded . '.' . $payloadEncoded);
    if (!hash_equals($signature, $expectedSignature)) {
      throw new Exception('Invalid token signature');
    }

    // Decode payload
    $payload = json_decode($this->base64UrlDecode($payloadEncoded), true);
    if (!$payload) {
      throw new Exception('Invalid token payload');
    }

    // Check expiration
    if (isset($payload['exp']) && $payload['exp'] < time()) {
      throw new Exception('Token has expired');
    }

    // Check not before
    if (isset($payload['nbf']) && $payload['nbf'] > time()) {
      throw new Exception('Token not valid yet');
    }

    return $payload;
  }

  /**
   * Decode token without validation (for debugging)
   */
  public function decodeToken($token)
  {
    $parts = explode('.', $token);
    if (count($parts) !== 3) {
      return null;
    }

    $header = json_decode($this->base64UrlDecode($parts[0]), true);
    $payload = json_decode($this->base64UrlDecode($parts[1]), true);

    return [
      'header' => $header,
      'payload' => $payload
    ];
  }

  /**
   * Tạo header cho JWT
   */
  private function createHeader()
  {
    return [
      'typ' => 'JWT',
      'alg' => $this->algorithm
    ];
  }

  /**
   * Tạo payload với timing claims
   */
  private function createPayload($data)
  {
    $now = time();

    $payload = [
      'iat' => $now, // issued at
      'exp' => $now + $this->expirationTime, // expiration
      'nbf' => $now // not before
    ];

    // Merge user data
    if (is_array($data)) {
      $payload = array_merge($payload, $data);
    } else {
      $payload['data'] = $data;
    }

    return $payload;
  }

  /**
   * Tạo signature
   */
  private function createSignature($data)
  {
    switch ($this->algorithm) {
      case 'HS256':
        $signature = hash_hmac('sha256', $data, $this->secretKey, true);
        break;
      case 'HS384':
        $signature = hash_hmac('sha384', $data, $this->secretKey, true);
        break;
      case 'HS512':
        $signature = hash_hmac('sha512', $data, $this->secretKey, true);
        break;
      default:
        throw new Exception('Unsupported algorithm');
    }

    return $this->base64UrlEncode($signature);
  }

  /**
   * Base64 URL Encode
   */
  private function base64UrlEncode($data)
  {
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
  }

  /**
   * Base64 URL Decode
   */
  private function base64UrlDecode($data)
  {
    return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
  }

  /**
   * Set expiration time
   */
  public function setExpirationTime($seconds)
  {
    $this->expirationTime = $seconds;
    return $this;
  }

  /**
   * Get expiration time
   */
  public function getExpirationTime()
  {
    return $this->expirationTime;
  }

  /**
   * Set secret key
   */
  public function setSecretKey($key)
  {
    $this->secretKey = $key;
    return $this;
  }

  /**
   * Get token expiration from token
   */
  public function getTokenExpiration($token)
  {
    try {
      $payload = $this->validateToken($token);
      return isset($payload['exp']) ? $payload['exp'] : null;
    } catch (Exception $e) {
      return null;
    }
  }

  /**
   * Check if token is expired
   */
  public function isTokenExpired($token)
  {
    $exp = $this->getTokenExpiration($token);
    return $exp ? $exp < time() : true;
  }

  /**
   * Get remaining time in seconds
   */
  public function getTokenRemainingTime($token)
  {
    $exp = $this->getTokenExpiration($token);
    return $exp ? max(0, $exp - time()) : 0;
  }
}
