var keepMenu;

function showMenu(id) { $('#'+id).style.display = 'block'; }
function hideMenu(id) { $('#'+id).style.display = 'none'; }
function toggleMenu(id) { if($('#'+id).style.display == 'block') hideMenu(id); else showMenu(id); }
function doKeepMenu() { keepMenu = true; }
function unKeepMenu() { keepMenu = false; }