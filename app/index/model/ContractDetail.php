<?php
namespace app\index\model;
use think\Model;
class ContractDetail extends Model{
    protected $table="coa_contract_info";


    public function getDetail($contract_id){
        $res = $this ->alias("a")
            ->join("coa_goods b","a.good_id = b.goods_id")
            ->where(["contract_id"=>$contract_id])
            ->field("b.goods_id,b.goods_name,a.count,a.price,a.total,a.currency,a.remark")
            ->select();
        return $res;
    }
}
