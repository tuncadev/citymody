function auto_private_when_roles_assigned(id,str){
    var parent_id = "mo_"+str+"_roles_" + id;
    var parent_id_private = "mo_"+str+"_login_valid_" + id ;
    var parent_id_role_value = document.getElementById(parent_id).value;
    document.getElementsByClassName(parent_id_private)[0].checked = false;
    if (parent_id_role_value!="") {
        document.getElementsByClassName(parent_id_private)[0].checked = true;
    }
}