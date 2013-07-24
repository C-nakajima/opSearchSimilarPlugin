<?php

class searchSimilarComponents extends sfComponents
{
  public function executeSearchSimilarGadget(sfWebRequest $request)
  {
    #自分のメンバーIDを取得
    $this->memberId = $this->getUser()->getMember()->getId();
    #自分の所属するコミュニティをランダムに5件取得
    $this->communities = Doctrine::getTable('Community')->retrievesByMemberId($this->memberId, 5, true);
    #ランダムに取得したコミュニティを検索用配列に保存
    $this->communitiesId = '0';
    foreach ($this->communities as $value) {
      $this->communitiesId = $this->communitiesId . ',' . $value['id'];
    }
    #自分が所属するコミュニティ2つ以上に所属するメンバーのメンバーIDをランダムに3件取得
    $this->similars =  Doctrine_Query::create()
                      # ->select('member_id, COUNT(id) as member_id')
                       ->select('member_id')
                       ->from('CommunityMember')
                      # ->Where('is_pre = ?', '0')
                      # ->andWhereNotIn('member_id', $this->memberId)
                      # ->andWhereIn('community_id', array(1))
                      # ->having('num_phonenumbers >= 2')
                      # ->groupBy('member_id')
                      # ->orderBy('random()')
                      # ->limit(3)
;
  }
}
