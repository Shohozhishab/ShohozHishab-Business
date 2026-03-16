<?php namespace App\Libraries;

class TransactionLog{
    /**
     * @description This method insert log
     * @param string $table
     * @param int $id
     * @param int $transactionId
     * @param float $amount
     * @return void
     */
    public function insert_log_data($table,$id = null,$transactionId = null,$amount,$dep_id =null,$wthd_id =null,$invoiceId = null,$purchaseId=null,$columName = null)
    {
        $data = [
            'table_name'  => $table ?: null,
            'sch_id'      => $_SESSION['shopId'],
            'id'          => $id ?: null,
            'trans_id'    => $transactionId ?: null,
            'dep_id'      => $dep_id ?: null,
            'wthd_id'     => $wthd_id ?: null,
            'invoice_id'    => $invoiceId ?: null,
            'purchase_id' => $purchaseId ?: null,
            'colum_name'  => $columName ?: null,
            'amount'      => $amount ?: null,
        ];

        $dbTable = DB()->table('transaction_log');
        $dbTable->insert($data);
    }

    /**
     * @description This method get table name by row
     * @param string $tableName
     * @param int $transactionId
     * @return void
     */
    public function get_table_name_by_row($tableName,$transactionId){
        $table = DB()->table('transaction_log');
        return $table->where('trans_id',$transactionId)->where('table_name',$tableName)->get()->getRow();
    }

    /**
     * @param string $table_name
     * @param int $id
     * @param int $trans_id
     * @param int $user_id
     * @param float $old_amount
     * @param float $new_amount
     * @return void
     */
    public function transaction_edit_log_data_insert($table_name,$id,$trans_id,$user_id,$old_amount,$new_amount,$invoiceId = null,$purchase_id = null,$columName = null){
        $data = [
            'table_name' => $table_name,
            'sch_id'     => $_SESSION['shopId'],
            'id' => $id,
            'trans_id' => $trans_id,
            'user_id' => $user_id,
            'invoice_id' => $invoiceId,
            'purchase_id' => $purchase_id,
            'old_amount' => $old_amount,
            'new_amount' => $new_amount,
            'colum_name' => $columName,
        ];
        $table = DB()->table('transaction_edit_log');
        $table->insert($data);
    }

    /**
     * @description This method transaction log update
     * @param int $transactionId
     * @param string $table
     * @param float $amount
     * @return void
     */
    public function transaction_log_update($transactionId,$table,$amount){
        $data = [
            'amount' => $amount
        ];
        $builder = DB()->table('transaction_log');
        $builder->where('trans_id',$transactionId)->where('table_name',$table)->update($data);
    }

    /**
     * @description This method transaction log all amount update
     * @param int $transactionId
     * @param float $amount
     * @return void
     */
    public function transaction_log_all_amount_update($transactionId,$amount){
        $data = [
            'amount' => $amount
        ];
        $builder = DB()->table('transaction_log');
        $builder->where('trans_id',$transactionId)->update($data);
    }

    /**
     * @description This method transaction deposit log update
     * @param int $depositId
     * @param string $table
     * @param float $amount
     * @return void
     */
    public function transaction_deposit_log_update($depositId,$amount){
        $data = [
            'amount' => $amount
        ];
        $builder = DB()->table('transaction_log');
        $builder->where('dep_id',$depositId)->update($data);
    }

    /**
     * @description This method get table name or dep_id by row
     * @param string $tableName
     * @param int $dep_id
     * @return mixed
     */
    public function get_table_name_or_dep_id_by_row($tableName,$dep_id){
        $table = DB()->table('transaction_log');
        return $table->where('dep_id',$dep_id)->where('table_name',$tableName)->get()->getRow();
    }

    /**
     * @description This method transaction withdraw log update
     * @param int $wthd_id
     * @param string $table
     * @param float $amount
     * @return void
     */
    public function transaction_withdraw_log_update($wthd_id,$amount){
        $data = [
            'amount' => $amount
        ];
        $builder = DB()->table('transaction_log');
        $builder->where('wthd_id',$wthd_id)->update($data);
    }

    /**
     * @description This method get table name or wthd_id by row
     * @param string $tableName
     * @param int $wthd_id
     * @return mixed
     */
    public function get_table_name_or_wthd_id_by_row($tableName,$wthd_id){
        $table = DB()->table('transaction_log');
        return $table->where('wthd_id',$wthd_id)->where('table_name',$tableName)->get()->getRow();
    }

    public function get_table_name_by_row_purchase_id($tableName,$purchaseId){
        $table = DB()->table('transaction_log');
        return $table->where('purchase_id',$purchaseId)->where('table_name',$tableName)->get()->getRow();
    }
    public function get_table_name_by_row_purchase_id_by_colum_name($tableName,$purchaseId,$column){
        $table = DB()->table('transaction_log');
        return $table->where('purchase_id',$purchaseId)->where('table_name',$tableName)->where('colum_name',$column)->get()->getRow();
    }
    public function get_table_name_by_row_invoice_id($tableName,$invoiceId){
        $table = DB()->table('transaction_log');
        return $table->where('invoice_id',$invoiceId)->where('table_name',$tableName)->get()->getRow();
    }
    public function get_table_name_by_row_invoice_id_by_colum_name($tableName,$invoiceId,$column){
        $table = DB()->table('transaction_log');
        return $table->where('invoice_id',$invoiceId)->where('table_name',$tableName)->where('colum_name',$column)->get()->getRow();
    }



}