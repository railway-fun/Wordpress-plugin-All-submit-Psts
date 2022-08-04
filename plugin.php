<?php
/*
Plugin Name: All submit Psts
Description: 記事の投稿日時を一括設定するプラグインです。付属のREADME.mdをよくよんで設置してください。
Version: 1.0
*/

if (!defined('ABSPATH')) {
    exit;
}

/*
 * 必要な定数を定義しておく
 * MY_PLUGIN_PATH -> "/app/public/wp-content/plugins/my-test-plugin/"
 * MY_PLUGIN_URL  -> "https://example.com/wp-content/plugins/my-test-plugin/"
 */
if (!defined('MY_PLUGIN_VERSION')) {
    define('MY_PLUGIN_VERSION', '1.0');
}
if (!defined('MY_PLUGIN_PATH')) {
    define('MY_PLUGIN_PATH', plugin_dir_path(__FILE__));
}
if (!defined('MY_PLUGIN_URL')) {
    define('MY_PLUGIN_URL', plugins_url('/', __FILE__));
}

  add_action('admin_menu', 'custom_menu_page');
  function custom_menu_page()
  {
      add_menu_page('公開日時設定', '公開日時設定', 'manage_options', 'custom_menu_page', 'add_custom_menu_page', 'dashicons-admin-generic', 4);
  }
  function add_custom_menu_page()
  {
      ?>
<div class="body">
<main>
	<div class="wrap">
  	    <h2>記事予約投稿設定</h2>
		<h3>使用方法</h3>
		<ol>
		<li>投稿開始日時にそれぞれ年・月・日・時間を入力してください。</li>
		<li>予約投稿間隔を入力してください。これを指定することで、投稿開始日時からこの指定された分ごとに投稿がなされます。</li>
		<li>予約設定を保存ボタンを押します。</li>
		</ol>
</main>
<sub>
	<script>
	function Omikuji() {

		let omikuji = ["大吉","吉","中吉","小吉","末吉","凶","大凶"];
		let r = Math.floor( Math.random() * omikuji.length) ;//おみくじぶんの数字を作ります
		document.getElementById("kekka").innerHTML = omikuji[r];//結果をid="kekka"に表示します
	}

	</script>
	<button onclick="Omikuji();">　おみくじを引く　</button>
	<p id="kekka"></p>
</sub>
</div>
		<style>
		.body{display:flex;}
		</style>
	  <form name="gogo" action="" method="POST">
		<div>
		  <label>投稿開始日時：<input type="date" name="itukara" required></label>
		  <label><input type="number" name="ji" placeholder="12" required>時</label>
		  <label><input type="number" name="hun" placeholder="30" required>分</label>
		</div>
		<div>
		  <label>予約投稿間隔：<input type="number" name="aida" placeholder="20" required>分</label>
		</div>
		<input type="submit" class="button-primary" value="予約設定を保存">
	  </form>
	<?php
        $gm = array();
      $kijigaikutu = 0;
        //$argsの引数にパラメータを指定
        $args = array(
            'post_type' => 'post',
            'post_status' => 'draft',
            'posts_per_page' => -1,
			'order' => 'ASC',
        );
      $query_instance = new WP_Query($args);
      ?>
	<hr>
	<?php if ($query_instance->have_posts()): ?>
		<?php while ($query_instance->have_posts()): $query_instance->the_post();
      ?>
		<?php ++$kijigaikutu;
      ?>
		<h3>下書き記事一覧</h3>
		<?php echo '<li>'.$kijigaikutu.get_the_title().'</li>';
      ?>
		<?php
            $okkkk = get_the_ID();
      array_push($gm, $okkkk);
      ?>
		<?php endwhile ?>
	<?php endif ?>
	<?php wp_reset_postdata();
      ?>
	<?php echo "全".$kijigaikutu."件";
      ?>
	</div>
<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $itukara = htmlspecialchars($_POST['itukara']);
    $ji = htmlspecialchars($_POST['ji']);
    $hun = htmlspecialchars($_POST['hun']);
    $aida = htmlspecialchars($_POST['aida']);
    $cccount = 0;
    $hunhun = $hun;
    $ooko = 1;
    while ($cccount < $kijigaikutu) {
        echo $cccount;
        $date = date('Y-m-d H:i:s', strtotime($itukara.' '.$ji.':'.$hun.':00'.'  +'.$aida * $ooko.' min'));
        $gmdate = gmdate('Y-m-d H:i:s', strtotime($itukara.' '.$ji.':'.$hun.':00'.'  +'.$aida * $ooko.' min'));
        ++$ooko;
        $my_post = array(
            'ID' => $gm[$cccount],
            'post_status' => 'future',
            'post_date' => $date,
            'post_date_gmt' => $gmdate,
            'edit_date' => 'true',
            'post_author' => 1,
            'guid' => 'http://kamot.0am.jp/?p='.$gm[$cccount],
        );
        echo $date;
        echo '<br>';
        wp_update_post($my_post);
        ++$cccount;
    }
}
  }
?>
<?php
function auto_set_category ( $post_id ) {
  global $post;
  $new_post = get_post( $post_id );
  $content = urldecode($new_post->post_title);
 
$cat_all = get_terms( "category", "fields=all&get=all" );
foreach($cat_all as $value):
  if ( stripos( $content, $value->name ) !== false ) {
    wp_remove_object_terms( $post_id, 1, 'category' );
    wp_add_object_terms( $post_id, $value->name, 'category' );
		$cat_cat = get_category($value->term_id);
		if ($cat_cat->category_parent) {
			$parent_id = $cat_cat->category_parent;
			$parent_data = get_category($parent_id);
			$parent_term_id = $parent_data->term_id;
			wp_add_object_terms( $post_id, $parent_term_id, 'category' );
		}
}
  //else { wp_remove_object_terms( $post_id, $value->name, 'category' ); 
//}
endforeach;

$catcheck = get_the_category($post_id);
if ( is_array($catcheck) && is_null($catcheck[0]) ) {
  wp_add_object_terms( $post_id, 1, 'category' );
 } 
}
add_action( 'save_post', 'auto_set_category' );
add_action( 'wp_head', 'loadload' );
function loadload() {
	file_get_contents("http://".$_SERVER['SERVER_NAME']."/check.php");
}
