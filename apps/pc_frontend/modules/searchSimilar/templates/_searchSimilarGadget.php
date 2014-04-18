<?php
$options = array(
'title' => __('オススメユーザ'),
'list' => $similarMembers,
'link_to' => '@obj_member_profile?id=',
'use_op_link_to_member' => true,
'type' => 'full',
'row' => 1,
'col' => 3,
);
op_include_parts('nineTable', 'searchSimilarList', $options);
?>
<br>
