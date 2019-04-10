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

    public function getContractType($contract_id){
        $res = $this->get($contract_id);
        return $res["contract_type"] == "c" ? "采购合同" : "销售合同";
    }

}
