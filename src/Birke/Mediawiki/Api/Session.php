<?php

/**
 * This file contains the class Session
 * 
 * @author Gabriel Birke <gb@birke-software.de>
 */

namespace Birke\Mediawiki\Api;


/**
 * Encapsulate MediaWiki API session logic
 *
 */
class Session {
    
    /**
     *
     * @var MediawikiApiClient
     */
    protected $client;
    
    /**
     * Cache for API tokens
     * @var array
     */
    protected $tokencache = array();
    
    function __construct(MediawikiApiClient $client) {
        $this->client = $client;
    }

    /**
     * Perform API login
     * 
     * If the API needs a token for logging in, the login is performed twice.
     *  
     * @param string $username
     * @param string $password
     * @throws SessionException
     */
    public function login($username, $password) {
        $credentials = array(
            'lgname' => $username,
            'lgpassword' => $password
        );
        $firstlogin = $this->client->login($credentials);
        $this->failOnError("Wiki login failed.", $firstlogin);
        $resultMsg = $firstlogin['login']['result'];
        if ($resultMsg != "NeedToken" && $resultMsg != "Success") {
            throw new SessionException("Wiki login failed: $resultMsg");
        }
        
        if ($resultMsg == "NeedToken") {
            $secondLogin = $this->client->login(array_merge(array(
                'lgtoken' => $firstlogin['login']['token']
            ), $credentials));
            $this->failOnError("Wiki login failed.", $secondLogin);
            $resultMsg = $secondLogin['login']['result'];
            if ($resultMsg != "Success") {
                throw new SessionException("Wiki login failed: $resultMsg");
            }
            $this->tokencache['login'] =  $firstlogin['login']['token'];
        }
    }
    
    public function logout() {
        $this->client->logout();
        $this->tokencache = array();
    }
    
    /**
     * Get Tokens for editing and other actions
     * See https://www.mediawiki.org/wiki/API:Tokens
     * @param array $type Token names
     * @return array Tokens
     * @throws SessionException
     */
    public function getTokens($type) {
        $tokens = $this->client->tokens(array('type' => implode('|', $type)));
        if (!empty($tokens['warnings']) || empty($tokens['tokens'])) {
            throw new SessionException("Error while getting tokens from wiki. ".json_encode($tokens));
        }
        $result = (array) $tokens['tokens'];
        $this->tokencache = array_merge($this->tokencache, $result );
        return $result;
    }
    
    /**
     * Get a single token from API or from cache if one was already fetched.
     * 
     * @param string $type
     * @param boolean $allowCache
     * @return type
     */
    public function getToken($type, $allowCache=true) {
        $tokenName = $type . 'token';
        if (!empty($this->tokencache[$tokenName]) && $allowCache) {
            return $this->tokencache[$tokenName];
        }
        $tokens = $this->getTokens(array($type));
        return $tokens[$tokenName];
    }
    
    /**
     * Get Edit Token, request from Mediawiki if necessary
     * @param boolean $allowCache Set false to force token query
     * @return string Edit Token
     * @throws SessionException
     */
    public function getEditToken($allowCache = true) {
        return $this->getToken('edit', $allowCache);
    }
    
    private function failOnError($message, $result) {
        if (!empty($result['error'])) {
            throw new SessionException($message." Response: ".json_encode($result));
        }
    }
    
    
    
}
