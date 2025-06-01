<?php

namespace App\Models;

use App\Core\Model;

class VnPayTransactions extends Model
{
  protected static $table = 'vnpay_transactions';
  protected static $primaryKey = 'id';

  public $id;
  public $booking_id;
  public $vnp_txn_ref;
  public $vnp_amount;
  public $vnp_order_info;
  public $vnp_transaction_no;
  public $vnp_response_code;
  public $vnp_transaction_status;
  public $vnp_pay_date;
  public $vnp_bank_code;
  public $vnp_bank_tran_no;
  public $vnp_card_type;
  public $vnp_secure_hash;
  public $payment_url;
  public $return_url_data;
  public $ipn_data;
  public $user_ip;
  public $user_agent;
  public $status;
  public $created_at;
  public $updated_at;
}
