<?php

require_once __DIR__ . '/env.php';

class Dbc
{
  protected $db_name = 'ojako_wp_db';
  protected $table_name = 'oja_enquete';

  // データベース接続関数
  protected function dbConnect() {
    $host   = DB_HOST;
    $dbname = DB_NAME;
    $user   = DB_USER;
    $pass   = DB_PASSWORD;
    $dsn    = "mysql:host=$host;dbname=$dbname;charset=utf8";

    try {
    $pdo = new PDO($dsn, $user, $pass,[
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
    // データベースがなければ作成
    $stmt = $pdo->prepare("CREATE DATABASE IF NOT EXISTS $this->db_name");
    // SQL実行
    $stmt->execute();

    // テーブル作成
    $sql = "CREATE TABLE IF NOT EXISTS $this->table_name (
      id INT(11) AUTO_INCREMENT PRIMARY KEY,
      answer1 INT(11) NOT NULL ,
      answer2_json VARCHAR(1024) NOT NULL,
      gender INT(11) NOT NULL,
      datetime DATETIME NOT NULL
    ) engine=innodb default charset=utf8";
    // SQL実行
    $res = $pdo->query($sql);
    } catch (PDOException $e) {
      // エラー発生
      echo $e->getMessage();
    } finally {
      // DBハンドルを返す
      return $pdo;
    }
  }

  /**
   * getResult()
   * アンケート集計結果を取得する
   * @return array $result; 検索結果
   */
  public function getResult() {
    $dbh = $this->dbConnect();
    // ①SQLの準備
    $sql = "SELECT * FROM $this->table_name";
    // ②SQLの実行
    $stmt = $dbh->query($sql);

    // ③SQLの結果を受け取る
    $result = $stmt->fetchall(PDO::FETCH_ASSOC);
    return $result;
    $dbh = null; //接続を終了する際にはnullを入れる
  }
} //class Dbc



?>