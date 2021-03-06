<?php
namespace app\index\model;
use think\model\Merge;
class Contract extends Merge
{
    protected $table="coa_contract";
    public function getContractType($contract_id){
        $res = $this->get($contract_id);
        return $res["contract_type"] == "c" ? "采购合同" : "销售合同";
    }

}
