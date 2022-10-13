<?php
//エラー検知用
ini_set('display_errors', "On");

require_once (__DIR__ . '/lib/enquete.php');
$enquete = new Enquete;


// アンケート用の配列変数を作成
$enq1 = array('まぁ普通', '普通', 'イマイチ', '好ましくない', 'なんか分からんがダメ');
$enq2 = array('デザイン', '操作性', '機能面', '将来性', 'その他おジャコについて');
$enq3 = array('秘密', '男性', '女性');
date_default_timezone_set('Asia/Tokyo');
$time = date("Y/m/d H:i:s");

// 送信ボタンが押されたとき
if (isset($_POST['submit'])) {
  $enquete->answer();
}
// ヘッダー出力
header("Content-Type: text/html;charset=UTF-8");

?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>アンケートアプリ</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <form action="index.php" method="POST">
    <?php
    // 条件により表示する画面を出し分ける
    if ( isset($_POST['submit']) && isset($_POST['ques1']) && isset($_POST['ques2'])) {
      print "<p>ご協力ありがとうございました。</p>\n";
      print "<p><input type=\"submit\" name=\"back\" value=\"前に戻る\"></p>\n";
      print "</form></body></html>";
      $enquete->show_result();
      exit;
    }
    ?>
    <p>簡単なアンケートです。ぜひご協力ください<br>
      <span class="errMsg">注：問１，問２は必須項目です</span>
    </p>
  
    <p>問１. 当サイトを閲覧してどう感じましたか？</p>
    <div class="enq_wrap">
      <?php
      for ($i=0; $i < count($enq1); $i++) {
        print "<div><input type=\"radio\" name=\"ques1\" value=\"$i\" id=\"ques1_$i\">
        <label for=\"ques1_$i\">"."$enq1[$i]</label></div>\n";
      }
      ?>
    </div>
    <p>問２． 当サイトで気になった点はございますか？（複数選択可）</p>
    <div class="enq_wrap">
      <?php
      for ($i=0; $i < count($enq2); $i++) {
        print "<div><input type=\"checkbox\" name=\"ques2[]\" value=\"$i\" id=\"ques2_$i\">
        <label for=\"ques2_$i\">"."$enq2[$i]</label></div>\n";
      }
      ?>
    </div>
  
    <p>問３． あなたの性別を教えて下さい</p>
    <div class="enq_wrap">
      <select name="sex">
      <?php
      print "<option value=\"0\" selected>{$enq3['0']}</option>";
      print "<option value=\"1\">{$enq3['1']}</option>";
      print "<option value=\"2\">{$enq3['2']}</option>";
      ?>
      </select>
    </div>
    <p>
      <input type="hidden" name="date" value="<?php echo $time; ?>">
      <input type="submit" value="アンケートに回答して集計結果を見てみる" name="submit">
      <input type="reset">
    </p>
  </form>
</body>
</html>