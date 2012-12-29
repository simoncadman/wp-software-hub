jQuery('.software-hub-view').tabs()
var installdropdown = document.getElementById('installdropdown');
if ( typeof installdropdown != 'undefined'  ) {
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

	if ( typeof type == "undefined" ) {
		type=document.getElementById('installdropdown').value;
	}

	softwareHubHideItems();
	var installdescription = document.getElementById('install' + type );
	if ( installdescription != null ) {
		installdescription.style.display = "";
	}
}

document.body.onload = function () {
	softwareHubUpdateInstallInstructions();
}

