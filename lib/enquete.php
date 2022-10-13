<?php

require_once __DIR__ . '/dbc.php';

class Enquete extends Dbc
{
  /**
   * answer()
   * 入力された答えをデータベースに登録
   * @var int $enq1, $enq3
   * @var string $enq2
   * @param 'index.phpにて定義したアンケート';
   * @return '';
   */
  public function answer() {
    global $enq1, $enq2, $enq3; //グローバル変数
    //問１の入力内容を変数に格納する
    for ($i=0; $i < count($enq1); $i++) {
      if ( $_POST['ques1'] == $i ) {
        $ans1 = $i;
        break;
      }
    }
    
    //問２の入力内容を変数に格納する
    for ($i=0; $i < count($enq2); $i++)
    // 質問の数分の値が入る配列をリセット
    $ans2[$i] = 0;
    for ($i=0; $i < count($_POST['ques2']); $i++)
    // チェックされた項目に「１」の値を入れる
    $ans2[$_POST['ques2'][$i]] = 1;
    //MYSQL保管用に一度jsonに変換
    $ans2_json = json_encode($ans2);
    // 問３の入力内容を変数に格納する
    for ($i=0; $i < count($enq3); $i++) {
      if ( $_POST['sex'] == $i ) {
        $ans3 = $i;
        break;
      }
    }
    //投稿日時
    $postdate = $_POST['date'];

    try {
      $pdo = $this->dbConnect();
      // トランザクションを開始
      $pdo->beginTransaction();
      //入力内容をデータベースに書き込む
      $sql = "INSERT INTO $this->table_name(answer1, answer2_json, gender, datetime)
                            VALUES (:answer1, :answer2_json, :gender, :datetime)";
      $stmt = $pdo->prepare($sql);
      $stmt->bindValue(':answer1', $ans1, PDO::PARAM_INT);
      $stmt->bindValue(':answer2_json', $ans2_json, PDO::PARAM_STR);
      $stmt->bindValue(':gender', $ans3, PDO::PARAM_INT);
      $stmt->bindValue(':datetime', $postdate, PDO::PARAM_STR);
      $res = $stmt->execute();
      if( $res ) {
        //コミット
        $pdo->commit();
      }
    } catch (PDOException $e) {
      //ロールバック
      $pdo->rollBack();
      exit($e);
    }
  }

  /**
   * show_result()
   * アンケートの集計結果をHMTLとして表示する
   */
  public function show_result() {
    global $enq1, $enq2, $enq3;//グローバル変数
    // すべてのデータを取得
    $ansData = $this->getResult();

    // データベースが存在しないとき
    if (!$ansData) {
      print "<p class='errMsg'>回答がありません</p>";
      exit;
    }

    //変数の初期化
    for ($i=0; $i < count($enq1); $i++)
      $res1[$i] = 0;
    for ($i=0; $i < count($enq2); $i++)
      $res2[$i] = 0;
    for ($i=0; $i < count($enq3); $i++)
      $res3[$i] = 0;

    // $ansDataをループさせて変数に追加代入
    $answer_json = array();
    foreach($ansData as $answer) {
      $res1[$answer["answer1"]]++;
      $res3[$answer["gender"]]++;
      //一度JSONデータを配列で取得
      $answer_json[] = $answer['answer2_json'];
    }

    foreach( $answer_json as  $val)
      //JSONデータを配列に変換
      $arr[] = json_decode($val, true);
    for ($i=0; $i < count($arr); $i++) {
      for ($j=0; $j < count($enq2); $j++) {
        //アンケートの対応する回答にそれぞれ合算
        $res2[$j] += $arr[$i][$j];
      }
    }

    // // 集計表を作成する
    print "<table>";
    print "<tr><th>問１</th><th class='ans'>結果</th></tr>";
    for ($i=0; $i < count($enq1); $i++)
      print "<tr><td>{$enq1[$i]}</td><td>{$res1[$i]}</td></tr>";
    print "</table>\n";

    print "<table>";
    print "<tr><th>問２</th><th class='ans'>結果</th></tr>";
    for ($i=0; $i < count($enq2); $i++) {
      print "<tr><td>{$enq2[$i]}</td><td>{$res2[$i]}</td></tr>";
    }
    print "</table>\n";

    print "<table>";
    print "<tr><th>問３</th><th class='ans'>結果</th></tr>";
    for ($i=0; $i < count($enq3); $i++)
      print "<tr><td>{$enq3[$i]}</td><td>{$res3[$i]}</td></tr>";
    print "</table>\n";

  }


}//class Enquete extends Dbc
?>