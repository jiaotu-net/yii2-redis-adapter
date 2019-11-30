<?php


class RedisException extends Exception {
    
}

class Redis {
    /*--------- 3.x -----------*/
    const REDIS_NOT_FOUND = 0;
    const REDIS_STRING = 1;
    const REDIS_SET = 2;
    const REDIS_LIST = 3;
    const REDIS_ZSET = 4;
    const REDIS_HASH = 5;
    const PIPELINE = 2;
    const ATOMIC = 0;
    const MULTI = 1;
    const OPT_SERIALIZER = 1;
    const OPT_PREFIX = 2;
    const OPT_READ_TIMEOUT = 3;
    const SERIALIZER_NONE = 0;
    const SERIALIZER_PHP = 1;
    const OPT_SCAN = 4;
    const SCAN_RETRY = 1;
    const SCAN_NORETRY = 0;
    const AFTER = 'after';
    const BEFORE = 'before';
    
    /*--------- 5.x -----------*/
    const REDIS_STREAM = 6;
    const OPT_TCP_KEEPALIVE = 6;
    const OPT_COMPRESSION = 7;
    const OPT_REPLY_LITERAL = 8;
    const OPT_COMPRESSION_LEVEL = 9;
    const SERIALIZER_IGBINARY = 2;
    const SERIALIZER_MSGPACK = 3;
    const SERIALIZER_JSON = 4;
    const COMPRESSION_NONE = 0;
    const COMPRESSION_LZF = 1;
    const COMPRESSION_ZSTD = 2;
    const COMPRESSION_ZSTD_MIN = 1;
    const COMPRESSION_ZSTD_DEFAULT = 3;
    const COMPRESSION_ZSTD_MAX = 22;
}