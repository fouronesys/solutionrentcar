<?php
class PreferenceData {
	public static $tablename = "preference";

		public static function getById($id){
		$sql = "select SQL_BIG_RESULT * from ".self::$tablename." where id=$id";
		$query = Executor::doit($sql);
		return Model::one($query[0],new PreferenceData());
	}


}

?>