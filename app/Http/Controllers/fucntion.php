<?php 
add_action( 'show_user_profile', 'my_show_extra_profile_fields' );
add_action( 'edit_user_profile', 'my_show_extra_profile_fields' );

function my_show_extra_profile_fields( $user ) { 
    global $wpdb;
 $userid = $user->ID;
 $user = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM application_form WHERE user_id =  $userid " ) );
$pservices_instructor =  $user->pservices_instructor; 
$pservice_types = $user->pservice_types;
$username = $user->username;
$pupload_photo = $user->pupload_photo;
$gender = $user->gender;
$city = $user->city;
$zipcode = $user->zipcode;
$specialties = $user->specialties;
$area_of_services = $user->area_of_services;
$background = $user->background;
$pservices_Offered = $user->pservices_Offered;
$ppersonal_pctivities = $user->ppersonal_pctivities;
$peducation_certifications = $user->peducation_certifications;
$pyears_of_experience = $user->pyears_of_experience;
$prate = $user->prate;
$pmethod_of_payment = $user->pmethod_of_payment;
$pemail = $user->pemail;
$pphone_no = $user->pphone_no;
$planguage = $user->planguage;
 ?>

	<h3>Extra profile information</h3>

	<table class="form-table">

		<tr>
			<th><label for="pservices_instructor">Type of Instructor</label></th>

			<td>
			<select id="pservices_instructor" name="pservices_instructor" class="form-control">
				<option value="%" <?php if('%'==$pservices_instructor ){echo "selected"; }?>>All</option>
				<option <?php if($pservices_instructor == 'Group Exercise Instructor') { echo "selected"; }?> value="Group Exercise Instructor">Group Exercise Instructor</option>
				<option <?php if($pservices_instructor == 'Personal Trainer') { echo "selected"; }?> value="Personal Trainer">Personal Trainer</option>
				<option <?php if($pservices_instructor == 'Massage Therapist') { echo "selected"; }?> value ="Massage Therapist">Massage Therapist</option>
				<option <?php if($pservices_instructor == 'Other') { echo "selected"; }?> value="Other">Other (Please Specify)</option>     
            </select>
				<span class="description">Type of Instructor</span>
			</td>
		</tr>

        <tr>
			<th><label for="pservice_types">Service Offered</label></th>

			<td>
			<select id="pservice_types" name="pservice_types" class="form-control">
                <option value="%">All</option>
                    <optgroup label="Group Exercise Instructor">
                                <option  value="group-tai-chi-instructor" <?php if('group-tai-chi-instructor'==$pservice_types ){echo "selected"; } ?>>Tai Chi Instructor</option>
                                <option  value="group-massage-therapist" <?php if('group-massage-therapist'==$pservice_types ){echo "selected"; } ?>>Massage Therapist</option>
                                <option  value="group-fall-prevention-instructors" <?php if('group-fall-prevention-instructors' ==$pservice_types ){echo "selected"; } ?>>Fall Prevention Instructors</option>
                                <option  value="group-yoga-instructor" <?php if('group-yoga-instructor'==$pservice_types ){echo "selected"; } ?>>Yoga Instructor</option>
                                <option  value="group-senior-fitness-instructors" <?php if('group-senior-fitness-instructors'==$pservice_types ){echo "selected"; } ?>>Senior Fitness Instructors</option>
                                <option  value="group-qi-gong-instructors" <?php if('group-qi-gong-instructors'==$pservice_types ){echo "selected"; } ?>>Qi Gong Instructors</option>
                                <option  value="group-personal-trainer" <?php if('group-personal-trainer'==$pservice_types ){echo "selected"; } ?>>Personal Trainer</option>
                                <option  value="group-zumba-instructor" <?php if('group-zumba-instructor'==$pservice_types ){echo "selected"; } ?>>Zumba Instructor</option>
                                <option  value="group-aerobic-instructors" <?php if('group-aerobic-instructors'==$pservice_types ){echo "selected"; } ?>>Aerobic Instructors</option>
                                <option  value="group-strength-training-instructor" <?php if('group-strength-training-instructor'==$pservice_types ){echo "selected"; } ?>>Strength Training Instructor</option>
                                <option  value="group-meditation-instructor" <?php if('group-meditation-instructor'==$pservice_types ){echo "selected"; } ?>>Meditation Instructor</option>
                    </optgroup>
                    <optgroup label="Personal Trainer">
                               <option  value="personal-tai-chi-instructor" <?php if('personal-tai-chi-instructor'==$pservice_types ){echo "selected"; } ?>>Tai Chi Instructor</option>
                                <option  value="personal-massage-therapist" <?php if('personal-massage-therapist'==$pservice_types ){echo "selected"; } ?>>Massage Therapist</option>
                                <option  value="personal-fall-prevention-instructors" <?php if('personal-fall-prevention-instructors'==$pservice_types ){echo "selected"; } ?>>Fall Prevention Instructors</option>
                                <option  value="personal-yoga-instructor" <?php if('personal-yoga-instructor'==$pservice_types ){echo "selected"; } ?>>Yoga Instructor</option>
                                <option  value="personal-senior-fitness-instructors" <?php if('personal-senior-fitness-instructors'==$pservice_types ){echo "selected"; } ?>>Senior Fitness Instructors</option>
                                <option  value="personal-qi-gong-instructors" <?php if('personal-qi-gong-instructors'==$pservice_types ){echo "selected"; } ?>>Qi Gong Instructors</option>
                                <option  value="personal-prsonal-trainer" <?php if('personal-prsonal-trainer'==$pservice_types ){echo "selected"; } ?>>Personal Trainer</option>
                                <option  value="personal-zumba-instructor" <?php if('personal-zumba-instructor'==$pservice_types ){echo "selected"; } ?>>Zumba Instructor</option>
                                <option  value="personal-aerobic-instructors" <?php if('personal-aerobic-instructors'==$pservice_types ){echo "selected"; } ?>>Aerobic Instructors</option>
                                <option  value="personal-strength-training-instructor" <?php if('personal-strength-training-instructor'==$pservice_types ){echo "selected"; } ?>>Strength Training Instructor</option>
                                <option  value="personal-meditation-instructor" <?php if('personal-meditation-instructor'==$pservice_types ){echo "selected"; } ?>>Meditation Instructor</option>
                    </optgroup>
                    <optgroup label="Massage Therapist">
                               <option  value="massage-tai-chi-instructor" <?php if('massage-tai-chi-instructor'==$pservice_types ){echo "selected"; } ?>>Tai Chi Instructor</option>
                                <option  value="massage-massage-therapist" <?php if('massage-massage-therapist'==$pservice_types ){echo "selected"; } ?>>Massage Therapist</option>
                                <option  value="massage-fall-prevention-instructors" <?php if('massage-fall-prevention-instructors'==$pservice_types ){echo "selected"; } ?>>Fall Prevention Instructors</option>
                                <option  value="massage-yoga-instructor" <?php if('massage-yoga-instructor'==$pservice_types ){echo "selected"; } ?>>Yoga Instructor</option>
                                <option  value="massage-senior-fitness-instructors" <?php if('massage-senior-fitness-instructors'==$pservice_types ){echo "selected"; } ?>>Senior Fitness Instructors</option>
                                <option  value="massage-qi-gong-instructors" <?php if('massage-qi-gong-instructors'==$pservice_types ){echo "selected"; } ?>>Qi Gong Instructors</option>
                                <option  value="massage-personal-trainer" <?php if('massage-personal-trainer'==$pservice_types ){echo "selected"; } ?>>Personal Trainer</option>
                                <option  value="'massage-zumba-instructor" <?php if('massage-zumba-instructor'==$pservice_types ){echo "selected"; } ?>>Zumba Instructor</option>
                                <option  value="massage-aerobic-instructors" <?php if('massage-aerobic-instructors'==$pservice_types ){echo "selected"; } ?>>Aerobic Instructors</option>
                                <option  value="massage-strength-training-instructor" <?php if('massage-strength-training-instructor'==$pservice_types ){echo "selected"; } ?>>Strength Training Instructor</option>
                                <option  value="massage-meditation-instructor" <?php if('massage-meditation-instructor'==$pservice_types ){echo "selected"; } ?>>Meditation Instructor</option>
                    </optgroup>
                    <optgroup label="Other (Please Specify)">
                                <option value="other-physical-therapist" <?php if('other-physical-therapist'==$pservice_types ){echo "selected"; }?>>Physical Therapist</option>
                                <option value="other-nutritionist-or-dietitian" <?php if('other-nutritionist-or-dietitian'==$pservice_types ){echo "selected";} ?>>Nutritionist or Dietitian</option>
								<option value="other-acupuncturist" <?php if('other-acupuncturist'==$pservice_types ){echo "selected"; }?>>Acupuncturist</option>
                                <option value="other-chiropractors" <?php if('other-chiropractors'==$pservice_types ){echo "selected";} ?> >Chiropractors</option'Chiropractors'
                                <option value="other-occupational-therapist" <?php if('other-occupational-therapist'==$pservice_types ){echo "selected";}  ?>>Occupational Therapist</option>
                                <option value="other-pilates-instructor" <?php if('other-pilates-instructor'==$pservice_types ){echo "selected"; } ?>>Pilates Instructor</option>
                                <option value="other-program-directors-or-managers" <?php if('other-program-directors-or-managers'==$pservice_types ){echo "selected"; }?>>Program Directors or Managers</option>
							    <option value="other-reiki-practitioner" <?php if('other-reiki-practitioner'==$pservice_types ){echo "selected"; }?>>Reiki Practitioner</option>
                                <option value="other-wellness-clinics" <?php if('other-wellness-clinics'==$pservice_types ){echo "selected";} ?>>Wellness Clinics</option>
                                <option value="other-wellness-Lifestyle-coach" <?php if('other-wellness-Lifestyle-coach'==$pservice_types ){echo "selected";} ?>>Wellness/Lifestyle Coach</option>
                                <option value="other-kung-fu" <?php if('other-kung-fu'==$pservice_types ){echo "selected";} ?>>Kung Fu</option>
                                <option value="other-Other" <?php if('other-Other'==$pservice_types ){echo "selected";}?> >Other entered by provider</option>
                    </optgroup>        
                </select>
				<span class="description">Service Offered</span>
			</td>
		</tr>

        <tr>
			<th><label for="gender">Gender</label></th>
			<td><input name="gender" value="Male"  type="radio" <?php if('Male'==$gender ){echo "checked='checked'"; }?>><span style="font-weight:normal; color:#000;">Male</span>
			<input name="gender" value="Female"  type="radio" <?php if('Female'==$gender ){echo "checked='checked'"; }?>><span style="font-weight:normal; color:#000;">Female</span>
			<span class="description">Gender</span>
			</td>
		</tr>

        <tr>
			<th><label for="city">City</label></th>

			<td>
				<input type="text" name="city" id="city" value="<?php echo $city; ?>" class="regular-text" /><br />
				<span class="description">City</span>
			</td>
		</tr>

        <tr>
			<th><label for="zipcode">Zipcode</label></th>

			<td>
				<input type="text" name="zipcode" id="zipcode" value="<?php echo $zipcode; ?>" class="regular-text" /><br />
				<span class="description">Zipcode</span>
			</td>
		</tr>

        <tr>
			<th><label for="specialties">Specialties</label></th>

			<td>
				<input type="text" name="specialties" id="specialties" value="<?php echo $area_of_services; ?>" class="regular-text" /><br />
				<span class="description">Specialties</span>
			</td>
		</tr>

        <tr>
			<th><label for="area_of_services">Areas of Service</label></th>

			<td>
				<input type="text" name="area_of_services" id="area_of_services" value="<?php echo $area_of_services; ?>" class="regular-text" /><br />
				<span class="description">Areas of Service</span>
			</td>
		</tr>

        <tr>
			<th><label for="background">Background</label></th>

			<td>
			<textarea name="background" id="background" class="regular-text" ><?php echo $background; ?></textarea><br />
				<span class="description">Background</span>
			</td>
		</tr>

        <tr>
			<th><label for="pservices_Offered">Services Offered </label></th>

			<td>
			<textarea name="pservices_Offered" id="pservices_Offered" class="regular-text" ><?php echo $pservices_Offered;
			?></textarea><br />
			<span class="description">Services Offered </span>
			</td>
		</tr>

        <tr>
			<th><label for="ppersonal_pctivities">Personal Activities</label></th>

			<td>
				<textarea name="ppersonal_pctivities" id="pppersonal_pctivitiesservices_Offered" class="regular-text" ><?php echo $ppersonal_pctivities; ?></textarea><br />
				<span class="description">Personal Activities</span>
			</td>
		</tr>

        <tr>
			<th><label for="peducation_certifications">	Education/Certifications</label></th>
			<td>
				<input type="text" name="peducation_certifications" id="peducation_certifications" value="<?php echo $peducation_certifications; ?>" class="regular-text" /><br />
				<span class="description">Education/Certifications</span>
			</td>
		</tr>

        <tr>
			<th><label for="pyears_of_experience">Years of Experience</label></th>
			<td>
				<input type="text" name="pyears_of_experience" id="peducation_certifications" value="<?php echo $pyears_of_experience; ?>" class="regular-text" /><br />
				<span class="description">Years of Experience</span>
			</td>
		</tr>

        <tr>
			<th><label for="prate">Rate</label></th>
			<td>
				<input type="text" name="prate" id="prate" value="<?php echo $prate; ?>" class="regular-text" /><br />
				<span class="description">Rate</span>
			</td>
		</tr>

        <tr>
			<th><label for="pmethod_of_payment">Method of Payment</label></th>
			<td>
				<input type="text" name="pmethod_of_payment" id="pmethod_of_payment" value="<?php echo $pmethod_of_payment; ?>" class="regular-text" /><br />
				<span class="description">Method of Payment</span>
			</td>
		</tr>
        <tr>
			<th><label for="pphone_no">Phone Number</label></th>
			<td>
				<input type="text" name="pphone_no" id="pphone_no" value="<?php echo $pphone_no; ?>" class="regular-text" /><br />
				<span class="description">Phone Number</span>
			</td>
		</tr>
        <tr>
			<th><label for="planguage">Language</label></th>
			<td>
				<input type="text" name="planguage" id="planguage" value="<?php echo $pmethod_of_payment; ?>" class="regular-text" /><br />
				<span class="description">Language</span>
			</td>
		</tr>
	</table>
<?php }
function save_extra_user_profile_fields( $user_id ) {

if ( !current_user_can( 'edit_user', $user_id ) ) { 
    return false; 
}
global $wpdb;
$pservices_instructor = $_POST['pservices_instructor'];
$pservice_types = $_POST['pservice_types'];
$username = $_POST['username'];
$gender = $_POST['gender'];
$city = $_POST['city'];
$zipcode = $_POST['zipcode'];
$specialties = $_POST['specialties'];
$area_of_services = $_POST['area_of_services'];
$background = $_POST['background'];
$pservices_Offered = $_POST['pservices_Offered'];
$peducation_certifications = $_POST['peducation_certifications'];
$prate = $_POST['prate'];
$pmethod_of_payment = $_POST['pmethod_of_payment'];
$planguage = $_POST['planguage'];
$email = $_POST['email'];
$pphone_no = $_POST['pphone_no'];
$ppersonal_pctivities = $_POST['ppersonal_pctivities'];
$pyears_of_experience = $_POST['pyears_of_experience'];
$first_name = $_POST['first_name'];
$last_name = $_POST['last_name'];
$name = $first_name." ".$last_name;

$wpdb->query($wpdb->prepare("UPDATE application_form SET pservices_instructor ='".$pservices_instructor."',pservice_types='".$pservice_types."', username ='".$name."',gender ='".$gender."', city='".$city."', zipcode ='".$zipcode."', specialties='".$specialties."', area_of_services='".$area_of_services."', background ='".$background."',pservices_Offered='".$pservices_Offered."',ppersonal_pctivities='".$ppersonal_pctivities."',peducation_certifications ='".$peducation_certifications."',pyears_of_experience='".$pyears_of_experience."',prate='".$prate."',pmethod_of_payment='".$pmethod_of_payment."',pemail='".$email."',pphone_no='".$pphone_no."',planguage='".$planguage."' WHERE user_id ='".$user_id."'"));  

}

add_action( 'personal_options_update', 'save_extra_user_profile_fields' );
add_action( 'edit_user_profile_update', 'save_extra_user_profile_fields' );

function custom_remove_user( $user_id ) {
    global $wpdb;
$wpdb->delete( 'application_form', array( 'user_id' => $user_id ), array( '%d' ) );
}
add_action( 'delete_user', 'custom_remove_user', 10 );

// add_filter('wp_nav_menu_items', 'add_login_logout_link', 10, 2);
// function add_login_logout_link($items, $args) {
//         ob_start();
//         wp_loginout('index.php');
//         $loginoutlink = ob_get_contents();
//         ob_end_clean();
//         $items .= '<li>'. $loginoutlink .'</li>';
//     return $items;
// }


add_action('admin_menu', 'application_section');    
function application_section(){
    add_menu_page('Application Form','Application Form','manage_options','applicat','application_form_page','',3);
}

function application_form_page(){
    $getdata=$_GET;
    $postdata=$_POST;
    switch($getdata['action']){
        case 'update-status':
            applicant_update_status($getdata,$postdata);
            break;
        case 'view':
            show_applicant($getdata,$postdata);
            break;
        case 'do_update':
            do_update($getdata,$postdata);
        default:
            show_applicant_listing($getdata,$postdata);
    }
}

function do_update($getdata,$postdata){
    if($postdata['status'] && $getdata['id']){
        global $wpdb;

        $data=[
            'id' => base64_decode($getdata['id']),
            'status' => $postdata['status']
        ];
        $q="UPDATE application_form set status='".$postdata['status']."' where form_id=".base64_decode($getdata['id']);
        
        $dat=$wpdb->query($q);
        
    }

    wp_redirect('http://www.fitness4ourseniors.com/wp-admin/admin.php?page=applicat');

}

function applicant_update_status($getdata,$postdata){
    global $wpdb;
    $item=$wpdb->get_row("SELECT * from application_form where form_id=".base64_decode($getdata['id']), ARRAY_A);
   
    ?>
    <div class="wrap">
        <h1>Update Status: <?php echo $item['username']; ?></h1>

        <form action="?action=do_update&page=applicat&id=<?php echo base64_encode($item['form_id']);?>" method="POST">
        <label for="username">Username</label><br/>
        <input type="text" readonly value="<?php echo $item['username']; ?>" name="username"/><br/><br/>
        <label for="email">Email</label><br/>
        <input type="text" readonly value="<?php echo $item['pemail']; ?>" name="email"/><br/><br/>
        <label for="username">Phone no.</label><br/>
        <input type="text" readonly value="<?php echo $item['pphone_no']; ?>" name="pphone_no"/><br/><br/>
        <label for="status">Status</label><br/>
        <select name="status" required>
            <option value="accept">Accept</option>
            <option value="reject">Reject</option>
        </select><br/><br/>
        <input type="submit" name="submit" value="Update Status"/>
        </form>
    </div>
    <?php
}
function show_applicant_listing($getdata, $postdata){
    
    ?>
    <link href="https://cdn.datatables.net/1.10.16/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css">
    <script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
    <div class="wrap">
    
    <h1 class="wp-heading-inline">
        <?php if($getdata['status']=='accept'){ echo 'Approved'; } ?>
        <?php if($getdata['status']=='reject'){ echo 'Rejected'; } ?>
        <?php if($getdata['status']==''){ echo 'Pending'; } ?> Applicant List</h1>
    <?php if($getdata['status']!='accept'){ echo '<a href="?status=accept&page=applicat" class="page-title-action">Approved list</a>'; } ?>
    <?php if($getdata['status']!='reject'){ echo '<a href="?status=reject&page=applicat" class="page-title-action">Rejected list</a>'; } ?>
    <?php if($getdata['status']!=''){ echo '<a href="?page=applicat" class="page-title-action">Pending list</a>'; } ?>

    <br/>
    <hr class="wp-header-end">
    <table id="table_id" class="display">
        <thead>
            <tr>
                <th>Username</th>
                <th>Email</th>
                <th>Instructor</th>
                <th>Service Types</th>
                <th>Submit Date</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php 
        global $wpdb;
        $query="SELECT * from application_form";
        if($getdata['status']!=''){
            $query.=" where status ='".$getdata['status']."'";
        } else {
            $query.=" where status=''";
        }
        
        $list=$wpdb->get_results($query, ARRAY_A);
        foreach($list as $item){
            
        ?>
            <tr>
                <td><?php echo $item['username']; ?></td>
                <td><?php echo $item['pemail']; ?></td>
                <td><?php echo $item['pservices_instructor']; ?></td>
                <td><?php echo $item['pservice_types']; ?></td>
                <td><?php echo $item['created_at']; ?></td>
                <td>
                        <?php
                        if( $item['status']==''){ echo 'pending';}
                        else 
                        { echo $item['status']; }
                        ?>
                </td>
                
                <td>
                <a href="?action=view&page=applicat&id=<?php echo base64_encode($item['form_id']);?>" >View</a> | 
                <a href="?action=update-status&page=applicat&id=<?php echo base64_encode($item['form_id']);?>" >Update Status</a>
                
                </td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
    </div>
    <script>
        jQuery(document).ready( function () {
            jQuery('#table_id').DataTable();
        } );
    </script>
    <?
}

function show_applicant($getdata,$postdata){
    global $wpdb;
    $item=$wpdb->get_row("SELECT * from application_form where form_id=".base64_decode($getdata['id']), ARRAY_A);
    ?>
    <div class="wrap">
    <h1>Application Form Details</h1>
    <hr class="wp-header-end">
    <table id="table_id" class="display" border="1" style="width:100%">
        <tbody>
            <tr>
                <th>Username</th>
                <td><?php echo $item['username']; ?></td>
                <th>Email</th>
                <td><?php echo $item['pemail']; ?></td>
            </tr>
            <tr>
                <th>Gender</th>
                <td><?php echo $item['gender']; ?></td>
                <th>City</th>
                <td><?php echo $item['city']; ?></td>
            </tr>
            <tr>
                <th>Zipcode</th>
                <td><?php echo $item['zipcode']; ?></td>
                <th>Specialties</th>
                <td><?php echo $item['specialties']; ?></td>
            </tr>
            <tr>
                <th>Phone no</th>
                <td><?php echo $item['pphone_no']; ?></td>
                <th>language</th>
                <td><?php echo $item['planguage']; ?></td>
            </tr>
            <tr>
                <th>Status </th>
                <td><?php echo $item['status']; ?></td>
                <th>Video</th>
                <td></td>
            </tr>
            <tr>
                <th>Service Instructor</th>
                <td><?php echo $item['pservices_instructor']; ?></td>
                <th>Service Type</th>
                <td><?php echo $item['pservice_types']; ?></td>
            </tr>
            <tr>
                <th>Area of Services</th>
                <td><?php echo $item['area_of_services']; ?></td>
                <th>Background</th>
                <td><?php echo $item['background']; ?></td>
            </tr>
            <tr>
                <th>Services Offered</th>
                <td><?php echo $item['pservices_Offered']; ?></td>
                <th>Personal Activity</th>
                <td><?php echo $item['ppersonal_pctivities']; ?></td>
            </tr>
            <tr>
                <th>Education Certifications</th>
                <td><?php echo $item['peducation_certifications']; ?></td>
                <th>Year of Experience</th>
                <td><?php echo $item['pyears_of_experience']; ?></td>
            </tr>
            <tr>
                <th>Rate</th>
                <td><?php echo $item['prate']; ?></td>
                <th>Method Of payment</th>
                <td><?php echo $item['pmethod_of_payment']; ?></td>
            </tr>
            
        </tbody>
    </table>
    </div>
    <?php
}


add_action('wp_ajax_nopriv_application_form_submit','process_application'); //for non logged in user
add_action('wp_ajax_application_form_submit','process_application'); //for nlogged in user

function process_application(){
    global $wpdb;
    $user=$wpdb->get_row('SELECT id FROM kujiwezahealing_users where user_email="'.$_POST['email'].'"',ARRAY_A);
   
     if($user && count($user)>0){
        $user_id=$user['id'];
    }  else {
         
         $user_data=[
                'user_login' => $_POST['email'],
                'user_email' => $_POST['email'],
                'user_nicename' => $_POST['name'],
                'user_pass' => rand(10000,99999),
                'user_regisered' => date('Y-m-d'),
                'user_status' => 0,
                'display_name' => $_POST['name']
            ];
       
        $user_id=wp_insert_user($user_data);
       
    }
    // IMAGE UPLOAD
    $target_dir = wp_upload_dir();
    $target_file = $target_dir['path'] . basename($_FILES["upload_image"]["name"]);

    // Check file size
    if ($_FILES["upload_image"]["size"] > 500000) {
        echo "Sorry, your file is too large.";
       
    }


    if (move_uploaded_file($_FILES["upload_image"]["tmp_name"], $target_file)) {
       $photoname=$target_dir['baseurl'].'/' . basename($_FILES["upload_image"]["name"]);
    } else {
        $photoname='';
    }
   
    //VIDEO UPLOAD
    $target_file = $target_dir['path'] . basename($_FILES["upload_video"]["name"]);

    if (move_uploaded_file($_FILES["upload_video"]["tmp_name"], $target_file)) {
       $videoname=$target_dir['baseurl'].'/' . basename($_FILES["upload_video"]["name"]);
    } else {
        $videoname='';
    }
    $check_exist=$wpdb->get_row("Select * from application_form where user_id=".$user_id);
    if($check_exist && count($check_exist)>0){

        $sql="UPDATE `application_form` SET `pservice_types`='".$_POST['Type_of_Instructor']."',`username`='".$_POST['name']."',`gender`='".$_POST['gender']."',`city`=[value-7],`zipcode`='".$_POST['zip']."',`specialties`='".$_POST['specialties']."',`area_of_services`='".$_POST['Areas_of_Service']."',`background`='".$_POST['background']."',`pservices_Offered`='".$_POST['Services_Offered']."',`ppersonal_pctivities`='".$_POST['Personal_Activities']."',`peducation_certifications`='".$_POST['Education_Certifications']."',`pyears_of_experience`='".$_POST['Years_of_Experience']."',`prate`='".$_POST['Rate']."',`pmethod_of_payment`='".$_POST['Method_of_Payment']."',`pemail`='".$_POST['email']."',`pphone_no`='".$_POST['pphone_no']."',`planguage`='".$_POST['language']."' WHERE user_id=".$user_id;

    } else {
        $sql = "INSERT INTO `application_form`(`pservices_instructor`, `pservice_types`, `username`, `pupload_photo`, `gender`, `city`, `zipcode`, `specialties`, `area_of_services`, `background`, `pservices_Offered`, `ppersonal_pctivities`, `peducation_certifications`, `pyears_of_experience`, `prate`, `pmethod_of_payment`, `pemail`, `pphone_no`, `planguage`, `user_id`, `video`) 
            VALUES ('','".$_POST['Type_of_Instructor']."','".$_POST['name']."','".$photoname."','".$_POST['gender']."','".$_POST['city']."','".$_POST['zip']."','".$_POST['specialties']."','".$_POST['Areas_of_Service']."','".$_POST['Services_Offered']."','".$_POST['background']."','".$_POST['Personal_Activities']."','".$_POST['Education_Certifications']."','".$_POST['Years_of_Experience']."','".$_POST['Rate']."','".$_POST['Method_of_Payment']."','".$_POST['email']."','".$_POST['pphone_no']."','".$_POST['language']."','".$user_id."','".$videoname."')";
    }

    $data=$wpdb->query($sql);
  
    wp_send_json($data);
}


add_action('wp_ajax_nopriv_mbf_login_action','mbf_login_action'); //for non logged in user
add_action('wp_ajax_mbf_login_action','mbf_login_action'); //for nlogged in user

function mbf_login_action(){
   // echo "asdfasdf";die;
 
 
    $credentials=[
        'user_login' => $_POST['user_login'],
        'user_pass' => $_POST['user_pass']
    ];
    $check=wp_authenticate( $_POST['user_login'], $_POST['user_pass'] );
    
    echo json_encode($check);
    die;
}