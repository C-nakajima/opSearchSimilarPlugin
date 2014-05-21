<?php

class searchSimilarComponents extends sfComponents
{
  public function executeSearchSimilarGadget(sfWebRequest $request)
  {
    #初期コミュニティ以外の自分が所属するコミュニティに2つ以上に所属しており、自分をアクセスブロックしていないフレンドではないメンバーのメンバーIDをランダムに3件取得

    $this->similarsId =  Doctrine::getTable('CommunityMember')->createQuery()
      ->select('member_id')
      ->whereIn('community_id', $this->getMyCommunities($this->getDefaultCommunities()))
      ->andWhereNotIn('member_id', $this->getUser()->getMember()->getId())
      ->andWhereNotIn('member_id', $this->getAccessBlockers())
      ->andWhereNotIn('member_id', $this->getFriendsMemberIds())
      ->andWhere('is_pre = ?', false)
      ->groupBy('member_id')
      ->having('COUNT(id) >= 2')
      ->orderBy('random()')
      ->limit(3)
      ->execute(array(), Doctrine::HYDRATE_NONE);

    $this->similarMembers = Doctrine_Collection::create('Member');
    foreach ($this->similarsId as $value){
      $this->similarMembers[] = Doctrine::getTable('Member')->find($value);
    }
  }

  private function getDefaultCommunities(){
    #初期コミュニティを取得
    $defaultCommunities = Doctrine::getTable('CommunityConfig')->createQuery()
      ->select('community_id')
      ->where('name = ?', 'is_default')
      ->andWhere('value = ?', true)
      ->execute(array(), Doctrine::HYDRATE_SINGLE_SCALAR);
     return $defaultCommunities;
  }

  private function getMyCommunities($defaultCommunities){
    #初期コミュニティ以外の自分の所属するコミュニティを取得
    $myCommunities = Doctrine::getTable('CommunityMember')->createQuery()
      ->select('community_id')
      ->where('is_pre = ?', false)
      ->andWhereNotIn('community_id' , $defaultCommunities)
      ->andWhereIn('member_id', $this->getUser()->getMember()->getId())
      ->execute(array(), Doctrine::HYDRATE_SINGLE_SCALAR);
     return $myCommunities;
  }

  private function getAccessBlockers(){
    #自分をアクセスブロックしているユーザを取得
    $accessBlockers = Doctrine::getTable('MemberRelationship')->createQuery()
      ->select('member_id_from')
      ->where('is_access_block = ?', true)
      ->andWhereIn('member_id_to', $this->getUser()->getMember()->getId())
      ->execute(array(), Doctrine::HYDRATE_SINGLE_SCALAR);
     return $accessBlockers;
  }

  private function getFriendsMemberIds(){
    #自分のフレンドを取得
    $friendsMemberIds =  Doctrine::getTable('MemberRelationShip')
      ->getFriendMemberIds($this->getUser()->getMember()->getId());
     return $friendsMemberIds;

  }
}
