<?php
$offset=$_POST['offset'];
if (!isset($offset)){
  $a=htmlentities($_SERVER['PHP_SELF']);
  echo "
    <form action=\"$a\" method=\"post\" id=\"setoffset\">
      <input id=\"offset\" type=\"hidden\" name=\"offset\" value=\"\" />
    </form>
    <script>
    var offset = new Date().getTimezoneOffset();
    console.log(offset);
    document.getElementById(\"offset\").value=offset;
    document.getElementById(\"offset\").value=Intl.DateTimeFormat().resolvedOptions().timeZone;
    console.log(document.getElementById(\"offset\").value);
    console.log(Intl.DateTimeFormat().resolvedOptions());
    // document.getElementById(\"setoffset\").submit();
    </script>
  ";
}
echo $offset;
