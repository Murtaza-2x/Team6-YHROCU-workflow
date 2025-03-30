<?php
function mapAuth0RolesToClearance($roles) {
    if (in_array("Admin", $roles)) return "admin";
    if (in_array("Manager", $roles)) return "manager";
    return "user";
}
?>
