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
	if( !in_array( $gid, OC_Group::getGroups())) {
		OCP\JSON::error(array('data' => array('title'=> $l->t('Modify Group Size') , 'message' => $l->t('Group does not exit') ))) ;
		exit();
	}
}
// Jawinton::end

if ( isset($_POST['group']) && isset($_POST['size']) ) {	//	Jawinton, add size
    // Jawinton::begin
    // Group size less than unallocated size
    $toAssigned = $size - OC_Group::getGroupSize($_POST['group']);
    if ($toAssigned > 0) {
        $storageInfo = OC_Helper::getStorageInfo();
        $groupAll = $storageInfo['total'];
        $groupAssigned = OC_Group::getGroupAssigned('admin');
        $groupUnassigned = $groupAll - $groupAssigned;
        if ($toAssigned > $groupUnassigned) {
            OC_JSON::error(array("data" => array('title'=> $l->t('Modify Group Size') , 'message' => $l->t('No enough space') )));
            exit();
        }
    }
    // Jawinton:: end


    // $result = OC_Group_Custom_Local::createGroup( $_POST['group'], $_POST['size'] ) ;	//	Jawinton, add size param
    $result = OC_Group::modifyGroupSize( $gid, $size );	//	Jawinton

    if ($result) {
        $tmpl = new OCP\Template("group_custom", "part.group");
        $tmpl->assign( 'groups' , OC_Group::getGroups() , true );	//	Jawinton
        $page = $tmpl->fetchPage();
        OCP\JSON::success(array('data' => array('page'=>$page)));
    } else {
        OCP\JSON::error(array('data' => array('title'=> $l->t('Modify Group Size') , 'message' => $l->t('Error while modifying group size') ))) ;
    }

}
