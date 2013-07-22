
var installdropdown = document.getElementById('installdropdown');
if ( installdropdown != null  ) {
	installdropdown.style.display = "";
	softwareHubHideItems();
}

function softwareHubHideItems ( ) {
	var items = document.getElementsByClassName('installtypes');
	for ( var i=0;i<items.length; i++ ) {
		items[i].style.display = "none";
	}
}

function softwareHubUpdateInstallInstructions ( type ) {

        if ( document.getElementById('installdropdown') != null ) {
            if ( typeof type == "undefined" ) {
                    type=document.getElementById('installdropdown').value;
            }

            softwareHubHideItems();
            var installdescription = document.getElementById('install' + type );
            if ( installdescription != null ) {
                    installdescription.style.display = "";
            }
        }
}

document.body.onload = function () {
        $('.software-hub-view').tabs();
	softwareHubUpdateInstallInstructions();
}

