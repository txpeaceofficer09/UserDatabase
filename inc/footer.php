	</body>
</html>

<?php

// if (isset($result)) @$result->free();
if (isset($mysqli)) $mysqli->close();

ob_end_flush();

?>