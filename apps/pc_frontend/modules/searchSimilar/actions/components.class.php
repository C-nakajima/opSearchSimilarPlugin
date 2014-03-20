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
    #初期コミュニティ以外の自分の所属するコミュニティを取得
    $this->communities = Doctrine::getTable('CommunityMember')->createQuery()
      ->select('community_id')
      ->where('is_pre = ?', false)
      ->andWhereNotIn('community_id' , $this->defaultCommunities)
      ->andWhereIn('member_id', $this->getUser()->getMember()->getId())
      ->execute(array(), Doctrine::HYDRATE_SINGLE_SCALAR);
    #自分をアクセスブロックしているユーザを取得
    $this->accessBlockers = Doctrine::getTable('MemberRelationship')->createQuery()
      ->select('member_id_from')
      ->where('is_access_block = ?', true)
      ->andWhereIn('member_id_to', $this->getUser()->getMember()->getId())
      ->execute(array(), Doctrine::HYDRATE_SINGLE_SCALAR);
    #((((初期コミュニティ以外の)&(自分が所属するコミュニティ))2つ以上に所属する)&(自分をアクセスブロックしていないメンバー))のメンバーIDをランダムに3件取得
    $this->similars =  Doctrine::getTable('CommunityMember')->createQuery()
      ->select('member_id')
      ->whereIn('community_id', $this->communities)
      ->andWhereNotIn('member_id', $this->getUser()->getMember()->getId())
      ->andWhereNotIn('member_id', $this->accessBlockers)
      ->andWhere('is_pre = ?', false)
      ->groupBy('member_id')
      ->having('COUNT(id) >= 2')
      ->orderBy('random()')
      ->limit(3)
      ->execute(array(), Doctrine::HYDRATE_SINGLE_SCALAR);
  }
}
