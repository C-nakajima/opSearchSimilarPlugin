<?php

class searchSimilarComponents extends sfComponents
{
  public function executeSearchSimilarGadget(sfWebRequest $request)
  {
    #初期コミュニティを取得
    $this->defaultCommunities = Doctrine::getTable('CommunityConfig')->createQuery()
      ->select('community_id')
      ->where('name = ?', 'is_default')
      ->andWhere('value = ?', true)
      ->execute(array(), Doctrine::HYDRATE_SINGLE_SCALAR);
    #自分の所属するコミュニティをランダムに5件取得
    $this->communities = Doctrine::getTable('CommunityMember')->createQuery()
      ->select('community_id')
      ->where('is_pre = ?', false)
      ->andWhereNotIn('community_id' , $this->defaultCommunities)
      ->andWhereIn('member_id', $this->getUser()->getMember()->getId())
      ->orderBy('random()')
      ->limit(5)
      ->execute(array(), Doctrine::HYDRATE_SINGLE_SCALAR);
    #自分が所属するコミュニティ2つ以上に所属するメンバーのメンバーIDをランダムに3件取得
    $this->similars =  Doctrine::getTable('CommunityMember')->createQuery()
      ->select('member_id')
      ->whereIn('community_id', $this->communitiesId)
      ->andWhereNotIn('member_id', $this->getUser()->getMember()->getId())
      ->andWhere('is_pre = ?', false)
      ->groupBy('member_id')
      ->having('COUNT(id) >= 2')
      ->orderBy('random()')
      ->limit(3)
      ->execute(array(), Doctrine::HYDRATE_SINGLE_SCALAR);
  }
}
