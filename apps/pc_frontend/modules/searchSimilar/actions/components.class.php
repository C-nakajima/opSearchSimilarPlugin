<?php

class searchSimilarComponents extends sfComponents
{
  public function executeSearchSimilarGadget(sfWebRequest $request)
  {
    #自分の所属するコミュニティをランダムに5件取得
    $this->communities = Doctrine::getTable('Community')->retrievesByMemberId($this->getUser()->getMember()->getId(), 5, true);
    #ランダムに取得したコミュニティを検索用配列に保存
    $this->communitiesId = array();
    foreach ($this->communities as $value) {
      $this->communitiesId[] = $value['id'];
    }
    #自分が所属するコミュニティ2つ以上に所属するメンバーのメンバーIDをランダムに3件取得
    $this->similars =  Doctrine::getTable('CommunityMember')->createQuery()
      ->select('member_id')
      ->andWhereIn('community_id', $this->communitiesId)
      ->andWhereNotIn('member_id', $this->getUser()->getMember()->getId())
      ->groupBy('member_id')
      ->having('COUNT(id) >= 2')
      ->orderBy('random()')
      ->limit(3)
      ->execute(array(), Doctrine::HYDRATE_NONE);
  }
}
