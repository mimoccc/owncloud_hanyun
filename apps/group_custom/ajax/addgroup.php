<?php

/**
 * ownCloud - group_custom
 *
 * @author Jorge Rafael García Ramos
 * @copyright 2012 Jorge Rafael García Ramos <kadukeitor@gmail.com>
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU AFFERO GENERAL PUBLIC LICENSE
 * License as published by the Free Software Foundation; either
 * version 3 of the License, or any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU AFFERO GENERAL PUBLIC LICENSE for more details.
 *
 * You should have received a copy of the GNU Affero General Public
 * License along with this library.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

// OCP\JSON::checkLoggedIn();
OCP\JSON::checkAdminUser(); // Jawinton
OCP\JSON::checkAppEnabled('group_custom');
OCP\JSON::callCheck();

$l = OC_L10N::get('group_custom');

$gid = $_POST['group'];
$size = OC_Helper::computerFileSize($_POST['size']);


// Jawinton::begin
if ( isset($gid) ) {
	if( in_array( $gid, OC_Group::getGroups())) {
		OCP\JSON::error(array('data' => array('title'=> $l->t('New Group') , 'message' => $l->t('Choose another name') ))) ;
		exit();
	}
}
// Jawinton::end

if ( isset($_POST['group']) && isset($_POST['size']) ) {	//	Jawinton, add size

    // Group size should be less than unused size
    $storageInfo = OC_Helper::getStorageInfo();
    $allStorage = $storageInfo['total'];
    $usedStorage = 0;
    $allUsers = OC_User::getUsers();
    foreach ($allUsers as $user) {
        $usedStorage += OC_Helper::computerFileSize(OC_User::getStorageInfo($user));
    }
    $unused = $allStorage - $usedStorage;
    if ($size > $unused) {
        OC_JSON::error(array("data" => array('title'=> $l->t('New Group') , 'message' => $l->t('No enough space') )));
        exit();
    }

    // $result = OC_Group_Custom_Local::createGroup( $_POST['group'], $_POST['size'] ) ;	//	Jawinton, add size param
    $result = OC_Group::createGroup( $gid, $size );	//	Jawinton

    if ($result) {

        $tmpl = new OCP\Template("group_custom", "part.group");
        $tmpl->assign( 'groups' , OC_Group::getGroups() , true );	//	Jawinton
        $page = $tmpl->fetchPage();

        OCP\JSON::success(array('data' => array('page'=>$page)));

    } else {

        OCP\JSON::error(array('data' => array('title'=> $l->t('New Group') , 'message' => $l->t('Error while creating group') ))) ;

    }

}
