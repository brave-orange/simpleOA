<?php
namespace app\index\model;
use think\model\Merge;
class Contract extends Merge
{
    protected $table="coa_contract";
    // 定义关联模型列表
    protected $relationModel = [
        // 给关联模型设置数据表
        'Contract_info'   =>  'coa_contract_info',
    ];
    // 定义关联外键
    protected $fk = 'contract_id';
    protected $mapFields = [
        // 为混淆字段定义映射

    ];
}
