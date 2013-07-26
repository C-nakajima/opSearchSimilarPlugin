検索したコミュニティのコミュニティID:
<?php
foreach ($communitiesId as $value) {
  echo $value . ',';
}
?>
<br>
2つ以上の同じコミュニティに参加しているメンバーのメンバーID:
<?php
foreach ($similars as $value) {
  echo $value[0] . ',';
}
