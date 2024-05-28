<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $procedure = "DROP PROCEDURE IF EXISTS `get_all_groups`;
      CREATE PROCEDURE `get_all_groups`( IN `as_on_date` varchar(191), IN `ac_type` varchar(191))
      BEGIN
    SELECT
    CIP.name as `Group Name`,
    SUM(IT.quantity * IT.type) as `Balance Qty`,
    FORMAT((IT.amount / SUM(IT.quantity * IT.type)),2) as RATE,
    IT.amount as `Value`
    FROM
    inventory_transactions IT
    JOIN
    chart_of_inventories CI ON CI.id = IT.coi_id
    JOIN
    chart_of_inventories CIP ON CIP.id = CI.parent_id
    WHERE
            IT.date <= as_on_date
            AND CI.rootAccountType = ac_type
    GROUP BY
    CIP.id;
END;";

        DB::unprepared($procedure);

        $procedure = "DROP PROCEDURE IF EXISTS `get_all_items`;

        CREATE PROCEDURE `get_all_items`( IN `as_on_date` varchar(191),IN `ac_type` varchar(191))
        BEGIN
SELECT
    `Group Name`,
    `Item ID`,
    `Item Name`,
    `Balance Qty`,
    `Rate`,
    `Value`
FROM (
    SELECT
        IF(Group_Name = @prev_group, '', IFNULL(Group_Name, '')) AS `Group Name`,
        IF(Subgroup_ID IS NULL, '', IFNULL(Subgroup_ID, '')) AS `Item ID`,
        IF(Subgroup_ID IS NULL, '', IFNULL(Subgroup_Name, '')) AS `Item Name`,
        `Balance Qty`,
        IF(Subgroup_ID IS NULL, '', IFNULL(IF(`AllQ` = 0, NULL,format( `AllA` / `AllQ`,2)), ''))  AS `Rate`,
        `Value`,
        @prev_group := Group_Name
    FROM (
        SELECT
            IF(CIP.name IS NULL, 'Total', CIP.name) AS Group_Name,
            CI.id AS Subgroup_ID,
            CI.name AS Subgroup_Name,
            SUM(IT.quantity * IT.type) AS `Balance Qty`,
             SUM(CASE WHEN IT.type = 1 THEN IT.quantity ELSE 0 END) as `AllQ`,
            SUM(CASE WHEN IT.type = 1 THEN IT.amount ELSE 0 END) as `AllA`,
            SUM(IT.amount) AS `Value`
        FROM
            inventory_transactions IT
        JOIN
            chart_of_inventories CI ON CI.id = IT.coi_id
        JOIN
            chart_of_inventories CIP ON CIP.id = CI.parent_id
         WHERE
            IT.date <= as_on_date
            AND CI.rootAccountType = ac_type
        GROUP BY
            CIP.id, CI.id WITH ROLLUP
    ) AS subquery,
    (SELECT @prev_group := null) AS prev
) AS result;

        END;";
        DB::unprepared($procedure);

        $procedure = "DROP PROCEDURE IF EXISTS `get_all_items_by_group`;

        CREATE PROCEDURE `get_all_items_by_group`( IN `group_id` INT, IN `as_on_date` varchar(191), IN `ac_type` varchar(191))
        BEGIN
SELECT
    `Item ID`,
    `Item Name`,
    `Balance Qty`,
    `Rate`,
    `Value`
FROM (
    SELECT
        IF(Group_Name = @prev_group, '', IFNULL(Group_Name, '')) AS `Group Name`,
        IF(Subgroup_ID IS NULL, '', IFNULL(Subgroup_ID, '')) AS `Item ID`,
        IF(Subgroup_ID IS NULL, '', IFNULL(Subgroup_Name, '')) AS `Item Name`,
        `Balance Qty`,
        IF(Subgroup_ID IS NULL, '', IFNULL(IF(`AllQ` = 0, NULL,format( `AllA` / `AllQ`,2)), ''))  AS `Rate`,
        `Value`,
        @prev_group := Group_Name
    FROM (
        SELECT
            IF(CIP.name IS NULL, 'Total', CIP.name) AS Group_Name,
            CI.id AS Subgroup_ID,
            CI.name AS Subgroup_Name,
            SUM(IT.quantity * IT.type) AS `Balance Qty`,
            SUM(CASE WHEN IT.type = 1 THEN IT.quantity ELSE 0 END) as `AllQ`,
            SUM(CASE WHEN IT.type = 1 THEN IT.amount ELSE 0 END) as `AllA`,
            SUM(IT.amount) AS `Value`
        FROM
            inventory_transactions IT
        JOIN
            chart_of_inventories CI ON CI.id = IT.coi_id
        JOIN
            chart_of_inventories CIP ON CIP.id = CI.parent_id
         WHERE
            CIP.id = group_id
            AND IT.date <= as_on_date
            AND CI.rootAccountType = ac_type
        GROUP BY
            CI.id WITH ROLLUP
    ) AS subquery,
    (SELECT @prev_group := null) AS prev
) AS result;

        END;";
        DB::unprepared($procedure);

        $procedure = "DROP PROCEDURE IF EXISTS `get_all_stores`;

        CREATE PROCEDURE `get_all_stores`(IN `as_on_date` varchar(191),IN `ac_type` varchar(191))
        BEGIN
SELECT
    `Store Name`,
    `Group Name`,
    `Item ID`,
    `Item Name`,
    `Balance Qty`,
    `Rate`,
    `Value`
FROM (
    SELECT
		IF(Store_Name = @prev_store, '', IFNULL(Store_Name, '')) AS `Store Name`,
        IF(Group_Name = @prev_group, '', IFNULL(Group_Name, '')) AS `Group Name`,
        IF(Subgroup_ID IS NULL, '', IFNULL(Subgroup_ID, '')) AS `Item ID`,
        IF(Subgroup_ID IS NULL, '', IFNULL(Subgroup_Name, '')) AS `Item Name`,
        `Balance Qty`,
        IF(Subgroup_ID IS NULL, '', IFNULL(IF(`AllQ` = 0, NULL,format( `AllA` / `AllQ`,2)), ''))  AS `Rate`,
        `Value`,
        @prev_group := Group_Name,
        @prev_store:= Store_Name
    FROM (
        SELECT
			ST.id AS Store_ID,
            ST.name AS Store_Name,
            IF(CIP.name IS NULL, 'Total', CIP.name) AS Group_Name,
            CI.id AS Subgroup_ID,
            CI.name AS Subgroup_Name,
            SUM(IT.quantity * IT.type) AS `Balance Qty`,
            SUM(CASE WHEN IT.type = 1 THEN IT.quantity ELSE 0 END) as `AllQ`,
            SUM(CASE WHEN IT.type = 1 THEN IT.amount ELSE 0 END) as `AllA`,
            SUM(IT.amount) AS `Value`
        FROM
            inventory_transactions IT
        JOIN
            chart_of_inventories CI ON CI.id = IT.coi_id
        JOIN
            chart_of_inventories CIP ON CIP.id = CI.parent_id
        LEFT JOIN
			stores ST on ST.id  = IT.store_id
		WHERE
            IT.date <= as_on_date
            AND CI.rootAccountType = ac_type
        GROUP BY
            CI.id WITH ROLLUP
    ) AS subquery,
    (SELECT @prev_group := null, @prev_store:= null) AS prev
) AS result;
        END;";
        DB::unprepared($procedure);

        $procedure = "DROP PROCEDURE IF EXISTS `get_all_items_by_store`;

        CREATE PROCEDURE `get_all_items_by_store`(IN `store_id` INT,IN `as_on_date` varchar(191),IN `ac_type` varchar(191))
        BEGIN
SELECT
    `Group Name`,
    `Item ID`,
    `Item Name`,
    `Balance Qty`,
    `Rate`,
    `Value`
FROM (
    SELECT
		IF(Store_Name = @prev_store, '', IFNULL(Store_Name, '')) AS `Store Name`,
        IF(Group_Name = @prev_group, '', IFNULL(Group_Name, '')) AS `Group Name`,
        IF(Subgroup_ID IS NULL, '', IFNULL(Subgroup_ID, '')) AS `Item ID`,
        IF(Subgroup_ID IS NULL, '', IFNULL(Subgroup_Name, '')) AS `Item Name`,
        `Balance Qty`,
        IF(Subgroup_ID IS NULL, '', IFNULL(IF(`AllQ` = 0, NULL,format( `AllA` / `AllQ`,2)), ''))  AS `Rate`,
        `Value`,
        @prev_group := Group_Name,
        @prev_store:= Store_Name
    FROM (
        SELECT
			ST.id AS Store_ID,
            ST.name AS Store_Name,
            IF(CIP.name IS NULL, 'Total', CIP.name) AS Group_Name,
            CI.id AS Subgroup_ID,
            CI.name AS Subgroup_Name,
            SUM(IT.quantity * IT.type) AS `Balance Qty`,
            SUM(CASE WHEN IT.type = 1 THEN IT.quantity ELSE 0 END) as `AllQ`,
            SUM(CASE WHEN IT.type = 1 THEN IT.amount ELSE 0 END) as `AllA`,
            SUM(IT.amount) AS `Value`
        FROM
            inventory_transactions IT
        JOIN
            chart_of_inventories CI ON CI.id = IT.coi_id
        JOIN
            chart_of_inventories CIP ON CIP.id = CI.parent_id
        LEFT JOIN
			stores ST on ST.id  = IT.store_id
        WHERE
			IT.store_id = store_id
			AND IT.date <= as_on_date
			 AND CI.rootAccountType = ac_type
        GROUP BY
             CI.id WITH ROLLUP
    ) AS subquery,
    (SELECT @prev_group := null, @prev_store:= null) AS prev
) AS result;

        END;";

        DB::unprepared($procedure);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared(
            'DROP PROCEDURE IF EXISTS get_all_groups;'
        );
        DB::unprepared(
            'DROP PROCEDURE IF EXISTS get_all_items;'
        );
        DB::unprepared(
            'DROP PROCEDURE IF EXISTS get_all_items_by_group;'
        );
        DB::unprepared(
            'DROP PROCEDURE IF EXISTS get_all_stores;'
        );
        DB::unprepared(
            'DROP PROCEDURE IF EXISTS get_all_items_by_store;'
        );
    }
};
