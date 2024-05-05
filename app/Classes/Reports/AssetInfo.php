<?php

namespace App\Classes\Reports;

use App\Models\Asset;
use App\Models\PreOrderItem;
use App\Models\StockIn;
use App\Models\StockOut;

use Illuminate\Support\Facades\DB;

class AssetInfo {
	public static function stockAssets() {
		$sql = "SELECT
					stock_ins.product_id,
				    GROUP_CONCAT(DISTINCT CONCAT(stock_ins.batch_number,'|',stock_ins.quantity,'|',stock_ins.buying_price) SEPARATOR ',') AS stock_in_data,
				    GROUP_CONCAT(DISTINCT CONCAT(st_out.batch_number,'|',st_out.out_qty) SEPARATOR ',') AS stock_out_data,
				    GROUP_CONCAT(DISTINCT CONCAT(stock_ins.batch_number,'|',(stock_ins.quantity - IFNULL(st_out.out_qty, 0))) SEPARATOR ',') AS stock_remain_data,
				    IFNULL(st_out.out_qty, 0) AS out_qty,
				    SUM(stock_ins.quantity - IFNULL(st_out.out_qty, 0)) AS remain_qty,
				    SUM(stock_ins.buying_price * (stock_ins.quantity - IFNULL(st_out.out_qty, 0) )) AS reamin_asset,
				    products.name
				FROM
					stock_ins
				LEFT JOIN products ON products.id = stock_ins.product_id
				LEFT JOIN (SELECT
				        stock_outs.product_id,
				        SUM(stock_outs.quantity) AS out_qty,
				        stock_outs.batch_number,
						stock_outs.sale_price,
				        products.name
				    FROM
				        stock_outs
				    LEFT JOIN products ON products.id = stock_outs.product_id
				    GROUP BY stock_outs.batch_number, stock_outs.product_id
				    ORDER BY stock_outs.product_id, stock_outs.batch_number) AS st_out ON
				    	st_out.batch_number = stock_ins.batch_number
				        AND st_out.product_id = stock_ins.product_id
				WHERE IFNULL(st_out.out_qty, 0) < stock_ins.quantity
				GROUP BY stock_ins.product_id
				ORDER BY stock_ins.product_id, stock_ins.batch_number";

		$result = DB::select($sql);

        return $result;
	}

	public static function sumStockAssets() {
		$sum = 0;
		
		foreach( self::stockAssets() as $row)
			$sum += $row->reamin_asset;

		return $sum;
	}

	public static function total_customer_due() {
		return abs(DB::select("SELECT SUM(change_amount) AS customer_due
							FROM `sales`
							WHERE change_amount < 0")[0]->customer_due);	
	}

	public static function total_purchase_due() {
		return abs(DB::select("SELECT SUM(change_amount) AS customer_due
							FROM `sales`
							WHERE change_amount < 0")[0]->customer_due);	
	}

	public static function supplier_due() {
		return 0;
	}
}