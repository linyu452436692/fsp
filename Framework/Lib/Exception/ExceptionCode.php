<?php
namespace FES\Lib\Exception;
/**
 * @desc 异常错误码, 错误码区间如下：
 *   1000-1599：http相关异常与http code相对应
 *   1600-1699: 原生异常类
 *   1700-1999: 自定义异常类
 * @author linyu@273.cn
 * @since 2015年11月13日
 */
class ExceptionCode {
    public static $code = [
        //仿http相关异常1000-1599
        'NotFoundException' => 1404,
        
        //原生异常类1600-1699
        'BadFunctionCallException' => 1600,
        'BadMethodCallException' => 1601,
        'DomainException' => 1602,
        'InvalidArgumentException' => 1603,
        'LengthException' => 1604,
        'LogicException' => 1605,
        'OutOfBoundsException' => 1606,
        'OutOfRangeException' => 1607,
        'OverflowException' => 1608,
        'RangeException' => 1609,
        'RuntimeException' => 1610,
        'UnderflowException' => 1611,
        'UnexpectedValueException' => 1612,
        
        //自定义异常类1700-1999
        'ExtensionException' => 1700,
    ];
}

