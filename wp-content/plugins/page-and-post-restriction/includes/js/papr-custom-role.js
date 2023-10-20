jQuery(function($){
            var page_selector_up = document.getElementById("current-page-selector");
            var page_selector_down = document.getElementById("current-page-selector-1");
            var link = 'admin.php?page=papr_custom_roles_sub_menu&curr=';

            page_selector_up.addEventListener("keyup", function(event) {
                if (event.keyCode === 13) {
                    page_selector_up_value = document.getElementById("current-page-selector").value;
                    var page_link = link.concat(page_selector_up_value);
                    window.open(page_link, "_self");
                }
            });

            page_selector_down.addEventListener("keyup", function(event) {
                if (event.keyCode === 13) {
                    page_selector_down_value = document.getElementById("current-page-selector-1").value;
                    var page_link = link.concat(page_selector_down_value);
                    window.open(page_link, "_self");
                }
            });

    function delete_role(){
        document.getElementById('papr_delete_custom_roles').submit();
        role_modal_close();
    }

    function role_modal_close(){
                var mo_modal = document.getElementById('papr_delete_role_modal');
                var span = document.getElementsByClassName("papr_close")[0];
                mo_modal.style.display = "none";
    }

    function papr_delete_role(role){
                var warning = 'Are you sure you want to delete ' + role + ' Role ?';
                document.getElementById("delete_role_warning").innerHTML = warning;
                var mo_modal = document.getElementById('papr_delete_role_modal');
                mo_modal.style.display = "block";
                document.getElementById('role_delete').value = role;
    }

    function cloneThisRole(role_id,clone_link) {
        link = clone_link + role_id + '_clone';
        document.getElementById('clone_role_link').href = link;
    }

    function hide_function(id) {
                var divs = document.getElementsByClassName('hide-category-div');
                var i;
                for (i = 0; i < divs.length; i++) {
                    divs[i].style.display = 'none';
                }

                var divid = document.getElementsByClassName(id);
                var i;
                for (i = 0; i < divid.length; i++) {
                    divid[i].style.display = '';
                }

                var div_button = document.getElementsByClassName('div_button');
                var i;
                for (i = 0; i < div_button.length; i++) {
                    div_button[i].style.backgroundColor = '#fff';
                }

                var id_button = id + '_button';
                document.getElementById(id_button).style.backgroundColor = '#f1f6ff';
    }

    function show_all_function() {
                var divs = document.getElementsByClassName('hide-category-div');
                var i;
                for (i = 0; i < divs.length; i++) {
                    divs[i].style.display = '';
                }

                var div_button = document.getElementsByClassName('div_button');
                var i;
                for (i = 0; i < div_button.length; i++) {
                    div_button[i].style.backgroundColor = '#fff';
                }

                var id_button = 'papr-tab-all_button';
                document.getElementById(id_button).style.backgroundColor = "#f1f6ff";
    }

});