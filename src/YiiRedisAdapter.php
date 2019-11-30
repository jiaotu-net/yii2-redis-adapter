<?php
namespace JiaoTu\RedisAdapter;

use yii\db\Exception;
use yii\redis\SocketException;

/**
 * Yii2 Redis Connection Adapter
 * @author haohui.wang
 *
 */
class YiiRedisAdapter {
    
    public const ERR_MSG_CONN_LOST = 'Connection lost';
    
    public const ERR_MSG_CONN_CLOSED = 'Connection closed';
    
    public const ERR_MSG_AUTH_FAILED = 'AUTH failed while reconnecting';
    
    public const ERR_MSG_SELECT_FAILED = 'SELECT failed while reconnecting';
    
    public const ERR_MSG_SOCKET_READ = 'socket error on read socket';
    
    public const ERR_MSG_READ_ERROR = 'read error on connection to ';
    
    public const ERR_MSG_WENT_AWAY = 'Redis server went away'; // Redis server %s:%d went away
    
    /**
     * 
     * @var \yii\redis\Connection
     */
    protected $conn = null;
    
    /**
     * Yii2 Redis Connection Version.
     * @var string
     */
    protected $srcVer = '2.0';
    
    /**
     * Virtual PhpRedis Extension Version.
     * @var string
     */
    protected $destVer = '5.0.0';
    
    /**
     * Virtual PhpRedis Version is 5.x
     * @var boolean
     */
    protected $ver5 = false;
    
    /**
     * Virtual PhpRedis Version is 4.x
     * @var boolean
     */
    protected $ver4 = false;
    
    /**
     * Virtual PhpRedis Version is 3.x
     * @var boolean
     */
    protected $ver3 = false;
    
    /**
     * Virtual PhpRedis Version is 2.x
     * @var boolean
     */
    protected $ver2 = false;
    
    /**
     * Virtual PhpRedis Version is 1.x
     * @var boolean
     */
    protected $ver1 = false;
    
    /**
     * Throw Excption 
     * @var boolean
     */
    private $_throw = true;
    
    /**
     * 
     * @param \yii\redis\Connection $yiiRedisConn
     * @param string $srcVer [optional] Yii2 Redis Connection Version. Default 2.0
     * @param string $destVer [optional] Virtual PhpRedis Extension Version. Default 5.0.0
     */
    public function __construct(& $yiiRedisConn, $srcVer = '2.0', $destVer = '5.0.0') {
        $this->conn = $yiiRedisConn;
        $this->srcVer = $srcVer;
        $this->destVer = $destVer;
        
        $varList = explode('.', $destVer);
        $mainVer = reset($varList);
        switch ($mainVer) {
            case '5':
                $this->ver5 = true;
                break;
            case '4':
                $this->ver4 = true;
                break;
            case '3':
                $this->ver3 = true;
                break;
            case '2':
                $this->ver2 = true;
                break;
            case '1':
                $this->ver1 = true;
                break;
            default:
                throw new \Exception('Virtual version not supported');
                break;
        }
        
        if (!class_exists('Redis')) {
            $dir = realpath(__DIR__);
            if (DIRECTORY_SEPARATOR != substr($dir, -1)) {
                $dir .= DIRECTORY_SEPARATOR;
            }
            require $dir . 'Typedef.php';
        }
    }
    
    public function __destruct() {
        
    }
    
    protected function _toggleThrowStatus($state = null) {
        if (null === $state) {
            if (!$this->ver5) {
                $this->_throw = false;
            }
        } else {
            $this->_throw = $state;
        }
    }

    public function & __call($name, $arguments) {
        try {
            $result = call_user_func_array(array($this->conn, $name), $arguments);
        } catch (SocketException $e) {
            if ($this->_throw) {
                $this->_toggleThrowStatus();
                throw new \RedisException(self::ERR_MSG_CONN_LOST);
            } else {
                return false;
            }
        } catch (Exception $e) {
            if (0 == $e->getCode()) {
                return false;
            }
            
            if ($this->_throw) {
                $this->_toggleThrowStatus();
                throw new \RedisException(self::ERR_MSG_CONN_LOST);
            } else {
                return false;
            }
        }
        
        return $result;
    }
    
    /**
     * Connects to a Redis instance.
     * @param string $host string. can be a host, or the path to a unix domain socket.
     * @param int $port [optional] 
     * @param float $timeout [optional] value in seconds (optional, default is 0 meaning unlimited)
     * @param null $reserved [optional] should be NULL if retryInterval is specified
     * @param int $retryInterval [optional]value in milliseconds (optional)
     * @param float $readTimeout [optional]value in seconds (optional, default is 0 meaning unlimited)
     * @return boolean true on success, false on error.
     */
    public function connect($host, $port = 6379, $timeout = 0, $reserved = null, $retryInterval = 0, $readTimeout = 0) {
        return true;
    }
    
    /**
     * Alias: connect
     * @param string $host string. can be a host, or the path to a unix domain socket.
     * @param int $port [optional] 
     * @param float $timeout [optional] value in seconds (optional, default is 0 meaning unlimited)
     * @param null $reserved [optional] should be NULL if retryInterval is specified
     * @param int $retryInterval [optional]value in milliseconds (optional)
     * @param float $readTimeout [optional]value in seconds (optional, default is 0 meaning unlimited)
     * @return boolean true on success, false on error.
     */
    public function open($host, $port = 6379, $timeout = 0, $reserved = null, $retryInterval = 0, $readTimeout = 0) {
        return $this->connect($host, $port, $timeout, $reserved, $retryInterval, $readTimeout);
    }
    
    /**
     * Connects to a Redis instance or reuse a connection already established with pconnect/popen.
     * @param string $host string. can be a host, or the path to a unix domain socket.
     * @param int $port [optional] 
     * @param float $timeout [optional] value in seconds (optional, default is 0 meaning unlimited)
     * @param string $persistentId [optional] identity for the requested persistent connection
     * @param int $retryInterval [optional]value in milliseconds (optional)
     * @param float $readTimeout [optional]value in seconds (optional, default is 0 meaning unlimited)
     * @return boolean true on success, false on error.
     */
    public function pconnect($host, $port = 6379, $timeout = 0, $persistentId = 'defalut', $retryInterval = 0, $readTimeout = 0) {
        return true;
    }
    
    /**
     * Alias: pconnect
     * @param string $host string. can be a host, or the path to a unix domain socket.
     * @param int $port [optional]
     * @param float $timeout [optional] value in seconds (optional, default is 0 meaning unlimited)
     * @param string $persistentId [optional] identity for the requested persistent connection
     * @param int $retryInterval [optional]value in milliseconds (optional)
     * @param float $readTimeout [optional]value in seconds (optional, default is 0 meaning unlimited)
     * @return boolean true on success, false on error.
     */
    public function popen($host, $port = 6379, $timeout = 0, $persistentId = 'defalut', $retryInterval = 0, $readTimeout = 0) {
        return $this->pconnect($host, $port, $timeout, $persistentId, $retryInterval, $readTimeout);
    }
    
    /**
     * Authenticate the connection using a password. Warning: The password is sent in plain-text over the network.
     * @param string $password
     * @return boolean true if the connection is authenticated, false otherwise.
     */
    public function auth ($password) {
        return true;
    }
    
    /**
     * Change the selected database for the current connection.
     * @param int $dbindex the database number to switch to.
     * @return boolean true in case of success, false in case of failure.
     */
    public function select ($dbindex) {
        try {
            $this->conn->select($dbindex);
        } catch (SocketException $e) {
            if ($this->_throw) {
                $this->_toggleThrowStatus();
                throw new \RedisException(self::ERR_MSG_CONN_LOST);
            } else {
                return false;
            }
        } catch (Exception $e) {
            if (0 == $e->getCode()) {
                return false;
            }
            
            if ($this->_throw) {
                $this->_toggleThrowStatus();
                throw new \RedisException(self::ERR_MSG_CONN_LOST);
            } else {
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Disconnects from the Redis instance.
     * @return boolean true on success, false on failure.
     */
    public function close() {
        return true;
    }
    
    /**
     * Set client option.
     * @param int $name 
     * @param mixed $value
     * @return boolean true on success, false on error.
     */
    public function setOption ($name, $value) {
        return true;
    }
    
    /**
     * Get client option.
     * @param int $name
     * @return mixed
     */
    public function getOption ($name) {
        return [];
    }
    
    /**
     * Check the current connection status.
     * @param string $message [optional]
     * @return mixed This method returns true on success, or the passed string if called with an argument.
     */
    public function ping ($message = '') {
        try {
            $result = $this->conn->ping($message ?: null);
        } catch (SocketException $e) {
            if ($this->_throw) {
                $this->_toggleThrowStatus();
                throw new \RedisException(self::ERR_MSG_CONN_LOST);
            } else {
                return false;
            }
        } catch (Exception $e) {
            if ($this->_throw) {
                $this->_toggleThrowStatus();
                throw new \RedisException(self::ERR_MSG_CONN_LOST);
            } else {
                return false;
            }
        }
        
        if ('' == $message) {
            if ($result) {
                return $this->ver5 ? true : '+PONG';
            }
            
            return false;
        }
        
        if ($result === $message) {
            return $this->ver5 ? $message : '+PONG';
        }
        
        return false;
    }

    /**
     * Returns the size of a list identified by Key.
     * If the list didn't exist or is empty, the command returns 0. If the data type identified by Key is not a list, the command return false.
     * @param string $key 
     * @return boolean|int The size of the list identified by Key exists; false if the data type identified by Key is not list
     */
    public function lLen($key) {
        try {
            $result = $this->conn->llen($key);
        } catch (SocketException $e) {
            if ($this->_throw) {
                $this->_toggleThrowStatus();
                throw new \RedisException(self::ERR_MSG_CONN_LOST);
            } else {
                return false;
            }
        } catch (Exception $e) {
            if (0 == $e->getCode()) {
                return false;
            }
            
            if ($this->_throw) {
                $this->_toggleThrowStatus();
                throw new \RedisException(self::ERR_MSG_CONN_LOST);
            } else {
                return false;
            }
        }
        
        return (int) $result;
    }

    /**
     * Alias: lLen
     * If the list didn't exist or is empty, the command returns 0. If the data type identified by Key is not a list, the command return false.
     * @param string $key
     * @return boolean|int The size of the list identified by Key exists; false if the data type identified by Key is not list
     */
    public function lSize($key) {
        return $this->lLen($key);
    }
    
    /**
     * Adds the string value to the head (left) of the list. Creates the list if the key didn't exist. If the key exists and is not a list, false is returned.
     * @param string $key
     * @param string $value value to push in key
     * @return int|boolean The new length of the list in case of success, FALSE in case of Failure.
     */
    public function lPush($key, $value) {
        try {
            $result = $this->conn->lpush($key, $value);
        } catch (SocketException $e) {
            if ($this->_throw) {
                $this->_toggleThrowStatus();
                throw new \RedisException(self::ERR_MSG_CONN_LOST);
            } else {
                return false;
            }
        } catch (Exception $e) {
            if (0 == $e->getCode()) {
                return false;
            }
            
            if ($this->_throw) {
                $this->_toggleThrowStatus();
                throw new \RedisException(self::ERR_MSG_CONN_LOST);
            } else {
                return false;
            }
        }
        
        return (int) $result;
    }
    
    /**
     * Adds the string value to the tail (right) of the list. Creates the list if the key didn't exist. If the key exists and is not a list, false is returned.
     * @param string $key
     * @param string $value value to push in key
     * @return int|boolean The new length of the list in case of success, false in case of Failure.
     */
    public function rPush($key, $value) {
        try {
            $result = $this->conn->rpush($key, $value);
        } catch (SocketException $e) {
            if ($this->_throw) {
                $this->_toggleThrowStatus();
                throw new \RedisException(self::ERR_MSG_CONN_LOST);
            } else {
                return false;
            }
        } catch (Exception $e) {
            if (0 == $e->getCode()) {
                return false;
            }
            
            if ($this->_throw) {
                $this->_toggleThrowStatus();
                throw new \RedisException(self::ERR_MSG_CONN_LOST);
            } else {
                return false;
            }
        }
        
        return (int) $result;
    }
    
    /**
     * Returns and removes the last element of the list.
     * @param string $key 
     * @return string if command executed successfully; false in case of failure (empty list).
     */
    public function rPop($key) {
        try {
            $result = $this->conn->rpop($key);
        } catch (SocketException $e) {
            if ($this->_throw) {
                $this->_toggleThrowStatus();
                throw new \RedisException(self::ERR_MSG_CONN_LOST);
            } else {
                return false;
            }
        } catch (Exception $e) {
            if (0 == $e->getCode()) {
                return false;
            }
            
            if ($this->_throw) {
                $this->_toggleThrowStatus();
                throw new \RedisException(self::ERR_MSG_CONN_LOST);
            } else {
                return false;
            }
        }
        
        return $result;
    }
    
    /**
     * Is a blocking lPop(rPop) primitive. 
     * If at least one of the lists contains at least one element, the element will be popped from the head of the list and returned to the caller. 
     * If all the list identified by the keys passed in arguments are empty, blPop will block during the specified timeout until an element is pushed to one of those lists. 
     * This element will be popped.
     * @param string|array $key containing the keys of the lists
     * @param int $timeout
     * @return array ['listName', 'element']
     */
    public function brPop(...$keys) {
        $timeout = 0;
        $params = [];
        $firstParam = reset($keys);
        $paramNum = count($keys);
        
        if ($paramNum < 1) {
            trigger_error ('Wrong parameter count for Redis::brPop()', E_USER_WARNING);
            return false;
        }
        
        if (2 == $paramNum) {
            $timeout = (int) $keys[1];
        }
        
        if (is_array($firstParam)) {
            if ($paramNum > 2) {
                trigger_error ('Wrong parameter count for Redis::brPop()', E_USER_WARNING);
                return false;
            }
            
            foreach ($firstParam as $value) {
                $params[] = $value;
            }
        } else {
            if (1 == $paramNum) {
                $params[] = $firstParam;
            } else {
                --$paramNum;
                foreach ($keys as $index => $value) {
                    if ($paramNum == $index) {
                        $timeout = (int) $value;
                    } else {
                        $params[] = $value;
                    }
                }
            }
        }
        
        $params[] = $timeout;
        
        try {
            $result = $this->conn->brpop(...$params);
        } catch (SocketException $e) {
            if ($this->_throw) {
                $this->_toggleThrowStatus();
                throw new \RedisException(self::ERR_MSG_CONN_LOST);
            } else {
                return false;
            }
        } catch (Exception $e) {
            if (0 == $e->getCode()) {
                return false;
            }
            
            if ($this->_throw) {
                $this->_toggleThrowStatus();
                throw new \RedisException(self::ERR_MSG_CONN_LOST);
            } else {
                return false;
            }
        }
        
        return $result;
    }
    
    /**
     * Returns the cardinality of an ordered set.
     * @param string $key
     * @return int Long, the set's cardinality
     */
    public function zCard($key) {
        try {
            $result = $this->conn->zcard($key);
        } catch (SocketException $e) {
            if ($this->_throw) {
                $this->_toggleThrowStatus();
                throw new \RedisException(self::ERR_MSG_CONN_LOST);
            } else {
                return false;
            }
        } catch (Exception $e) {
            if (0 == $e->getCode()) {
                return false;
            }
            
            if ($this->_throw) {
                $this->_toggleThrowStatus();
                throw new \RedisException(self::ERR_MSG_CONN_LOST);
            } else {
                return false;
            }
        }
        
        return (int) $result;
    }
    
    /**
     * Alias: zCard
     * Returns the cardinality of an ordered set.
     * @param string $key
     * @return int Long, the set's cardinality
     */
    public function zSize($key) {
        return $this->zCard($key);
    }
    
    /**
     * Returns the score of a given member in the specified sorted set.
     * @param string $key
     * @param string $member
     * @return double
     */
    public function zScore($key, $member) {
        try {
            $result = $this->conn->zscore($key, $member);
        } catch (SocketException $e) {
            if ($this->_throw) {
                $this->_toggleThrowStatus();
                throw new \RedisException(self::ERR_MSG_CONN_LOST);
            } else {
                return false;
            }
        } catch (Exception $e) {
            if (0 == $e->getCode()) {
                return false;
            }
            
            if ($this->_throw) {
                $this->_toggleThrowStatus();
                throw new \RedisException(self::ERR_MSG_CONN_LOST);
            } else {
                return false;
            }
        }
        
        if (null === $result) {
            return false;
        }
        
        return (float) $result;
    }
    
    /**
     * Delete one or more members from a sorted set.
     * @param string $key
     * @param string ...$member
     * @return int The number of members deleted.
     */
    public function zRem($key, ...$member) {
        try {
            $result = $this->conn->zrem($key, ...$member);
        } catch (SocketException $e) {
            if ($this->_throw) {
                $this->_toggleThrowStatus();
                throw new \RedisException(self::ERR_MSG_CONN_LOST);
            } else {
                return false;
            }
        } catch (Exception $e) {
            if (0 == $e->getCode()) {
                return false;
            }
            
            if ($this->_throw) {
                $this->_toggleThrowStatus();
                throw new \RedisException(self::ERR_MSG_CONN_LOST);
            } else {
                return false;
            }
        }
        
        if (null === $result) {
            return false;
        }
        
        return (int) $result;
    }
    
    /**
     * Alias: zRem
     * Delete one or more members from a sorted set.
     * @param string $key
     * @param string ...$member
     * @return int The number of members deleted.
     */
    public function zDelete($key, ...$member) {
        return $this->zRem($key, ...$member);
    }
    
    /**
     * Alias: zRem
     * Delete one or more members from a sorted set.
     * @param string $key
     * @param string ...$member
     * @return int The number of members deleted.
     */
    public function zRemove($key, ...$member) {
        return $this->zRem($key, ...$member);
    }
    
    /**
     * Add one or more members to a sorted set or update its score if it already exists
     * @param string $key
     * @param double $score
     * @param string $value
     * @return int Long 1 if the element is added. 0 otherwise.
     */
    public function zAdd($key, $score, $value) {
        $options = [$score, $value];
        
        try {
            $result = $this->conn->zadd($key, ...$options);
        } catch (SocketException $e) {
            if ($this->_throw) {
                $this->_toggleThrowStatus();
                throw new \RedisException(self::ERR_MSG_CONN_LOST);
            } else {
                return false;
            }
        } catch (Exception $e) {
            if (0 == $e->getCode()) {
                return false;
            }
            
            if ($this->_throw) {
                $this->_toggleThrowStatus();
                throw new \RedisException(self::ERR_MSG_CONN_LOST);
            } else {
                return false;
            }
        }
        
        return (int) $result;
    }
    
    /**
     * Returns the elements of the sorted set stored at the specified key which have scores in the range [start,end]. 
     * Adding a parenthesis before start or end excludes it from the range. 
     * +inf and -inf are also valid limits. 
     * @param string $key
     * @param string $start
     * @param string $end
     * @param array $options [optional] Two options are available: withscores => true, and limit => [$offset, $count]
     * @return array containing the values in specified range.
     */
    public function zRangeByScore($key, $start, $end, $options = []) {
        $params = [];
        $withscores = false;
        if (isset($options['withscores']) && $options['withscores']) {
            $params[] = 'WITHSCORES';
            $withscores = true;
        }
        if (isset($options['limit']) && !empty($options['limit']) && is_array($options['limit'])) {
            $params[] = 'LIMIT';
            foreach ($options['limit'] as $value) {
                $params[] = $value;
            }
        }
        
        try {
            $result = $this->conn->zrangebyscore($key, $start, $end, ...$params);
        } catch (SocketException $e) {
            if ($this->_throw) {
                $this->_toggleThrowStatus();
                throw new \RedisException(self::ERR_MSG_CONN_LOST);
            } else {
                return false;
            }
        } catch (Exception $e) {
            if (0 == $e->getCode()) {
                return false;
            }
            
            if ($this->_throw) {
                $this->_toggleThrowStatus();
                throw new \RedisException(self::ERR_MSG_CONN_LOST);
            } else {
                return false;
            }
        }
        
        if (!$withscores || empty($result)) {
            return $result;
        }
        
        $ret = [];
        $tmp = '';
        foreach ($result as $index => $value) {
            if (1 == ($index & 1)) {
                $ret[$tmp] = (float) $value;
            } else {
                $tmp = $value;
            }
        }
        
        return $ret;
    }
    
    /**
     * Returns the elements of the sorted set stored at the specified key which have scores in the range [start,end]. 
     * Adding a parenthesis before start or end excludes it from the range. 
     * +inf and -inf are also valid limits. 
     * zRevRangeByScore returns the same items in reverse order, when the start and end parameters are swapped.
     * @param string $key
     * @param string $start
     * @param string $end
     * @param array $options [optional] Two options are available: withscores => true, and limit => [$offset, $count]
     * @return array containing the values in specified range.
     */
    public function zRevRangeByScore($key, $start, $end, $options = []) {
        $params = [];
        $withscores = false;
        if (isset($options['withscores']) && $options['withscores']) {
            $params[] = 'WITHSCORES';
            $withscores = true;
        }
        if (isset($options['limit']) && !empty($options['limit']) && is_array($options['limit'])) {
            $params[] = 'LIMIT';
            foreach ($options['limit'] as $value) {
                $params[] = $value;
            }
        }
        
        try {
            $result = $this->conn->zrevrangebyscore($key, $start, $end, ...$params);
        } catch (SocketException $e) {
            if ($this->_throw) {
                $this->_toggleThrowStatus();
                throw new \RedisException(self::ERR_MSG_CONN_LOST);
            } else {
                return false;
            }
        } catch (Exception $e) {
            if (0 == $e->getCode()) {
                return false;
            }
            
            if ($this->_throw) {
                $this->_toggleThrowStatus();
                throw new \RedisException(self::ERR_MSG_CONN_LOST);
            } else {
                return false;
            }
        }
        
        if (!$withscores || empty($result)) {
            return $result;
        }
        
        $ret = [];
        $tmp = '';
        foreach ($result as $index => $value) {
            if (1 == ($index & 1)) {
                $ret[$tmp] = (float) $value;
            } else {
                $tmp = $value;
            }
        }
        
        return $ret;
    }
    
    /**
     * Set the string value in argument as value of the key. If you're using Redis >= 2.6.12, you can pass extended options as explained below
     * @param string $key 
     * @param string $value
     * @param mixed $options [optional]  If you pass an integer, phpredis will redirect to SETEX, and will try to use Redis >= 2.6.12 extended options if you pass an array with valid values
     * @return boolean true if the command is successful.
     */
    public function set($key, $value, $options = []) {
        $params = [];
        
        if (!empty($options)) {
            foreach ($options as $index => $value) {
                if (is_numeric($index)) {
                    $params[] = strtoupper($value);
                } else {
                    $params[] = strtoupper($index);
                    $params[] = strtoupper($value);
                }
            }
        }
        
        try {
            $result = $this->conn->set($key, $value, ...$params);
        } catch (SocketException $e) {
            if ($this->_throw) {
                $this->_toggleThrowStatus();
                throw new \RedisException(self::ERR_MSG_CONN_LOST);
            } else {
                return false;
            }
        } catch (Exception $e) {
            if (0 == $e->getCode()) {
                return false;
            }
            
            if ($this->_throw) {
                $this->_toggleThrowStatus();
                throw new \RedisException(self::ERR_MSG_CONN_LOST);
            } else {
                return false;
            }
        }
        
        if (null === $result) {
            return false;
        }
        
        return $result;
    }
    
    /**
     * Get the value related to the specified key
     * @param string $key
     * @return mixed String or Bool: If key didn't exist, false is returned. Otherwise, the value related to this key is returned.
     */
    public function get($key) {
        try {
            $result = $this->conn->get($key);
        } catch (SocketException $e) {
            if ($this->_throw) {
                $this->_toggleThrowStatus();
                throw new \RedisException(self::ERR_MSG_CONN_LOST);
            } else {
                return false;
            }
        } catch (Exception $e) {
            if (0 == $e->getCode()) {
                return false;
            }
            
            if ($this->_throw) {
                $this->_toggleThrowStatus();
                throw new \RedisException(self::ERR_MSG_CONN_LOST);
            } else {
                return false;
            }
        }
        
        if (null === $result) {
            return false;
        }
        
        return $result;
    }
    
    /**
     * An array of keys, or an undefined number of parameters, each a key: key1 key2 key3 ... keyN
     * @param string|array ...$keys
     * @return int Long Number of keys deleted.
     */
    public function del(...$keys) {
        $params = [];
        
        $firstParam = reset($keys);
        $paramNum = count($keys);
        
        if ($params < 1) {
            trigger_error ('Wrong parameter count for Redis::del()', E_USER_WARNING);
            return false;
        }
        
        if (is_array($firstParam)) {
            if ($paramNum > 1) {
                trigger_error ('Wrong parameter count for Redis::del()', E_USER_WARNING);
                return false;
            }
            
            foreach ($firstParam as $key) {
                $params[] = $key;
            }
        } else {
            $params = & $keys;
        }
        
        try {
            $result = $this->conn->del(...$params);
        } catch (SocketException $e) {
            if ($this->_throw) {
                $this->_toggleThrowStatus();
                throw new \RedisException(self::ERR_MSG_CONN_LOST);
            } else {
                return false;
            }
        } catch (Exception $e) {
            if (0 == $e->getCode()) {
                return false;
            }
            
            if ($this->_throw) {
                $this->_toggleThrowStatus();
                throw new \RedisException(self::ERR_MSG_CONN_LOST);
            } else {
                return false;
            }
        }
        
        return (int) $result;
    }
}