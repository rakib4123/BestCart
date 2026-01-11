<?php
require_once('../../controllers/helpers.php');
requireAdmin();
require_once('../../models/userModel.php');

// Partial AJAX refresh: return only table rows
if (isAjax() && (isset($_GET['partial']) && $_GET['partial'] === 'user_rows')) {
    $users = getAllUser();
    if (!empty($users)) {
        foreach ($users as $u) {
            ?>
            <tr>
                <td>#<?= $u['id'] ?></td>
                <td>
                    <div style="display:flex; align-items:center; gap:10px;">
                        <div style="width:32px; height:32px; background:#e2e8f0; border-radius:50%; display:flex; align-items:center; justify-content:center; font-weight:bold; color:#64748b;">
                            <?= strtoupper(substr($u['username'], 0, 1)) ?>
                        </div>
                        <b><?= htmlspecialchars($u['username']) ?></b>
                    </div>
                </td>
                <td><?= htmlspecialchars($u['email']) ?></td>
                <td>
                    <?php if (strtolower(trim($u['role'] ?? '')) === 'admin') { ?>
                        <span style="background:#dbeafe; color:#1e40af; padding:2px 8px; border-radius:4px; font-size:0.8rem; font-weight:bold;">Admin</span>
                    <?php } else { ?>
                        <span style="background:#f1f5f9; color:#475569; padding:2px 8px; border-radius:4px; font-size:0.8rem;">Customer</span>
                    <?php } ?>
                </td>
                <td>
                    <a href="edit_user.php?id=<?= $u['id'] ?>" class="btn btn-secondary" style="padding:5px 10px; font-size:0.8rem;">Edit</a>
                    <a
                      href="../../controllers/adminUserController.php?delete=<?= $u['id'] ?>"
                      class="btn btn-danger"
                      style="padding:5px 10px; font-size:0.8rem;"
                      data-ajax-link="true"
                      data-confirm="true"
                      data-confirm-text="Delete this user?"
                      data-refresh-target="#usersTableBody"
                      data-refresh-url="manage_users.php?partial=user_rows"
                    >Del</a>
                </td>
            </tr>
            <?php
        }
    }
    exit;
}

require_once('layout.php');

$users = getAllUser();
?>

<div class="header-title">Manage Users</div>

<div class="card">
    <h3 style="margin-bottom:15px; border-bottom:1px solid #eee; padding-bottom:10px;">Add New User</h3>

    <form method="post"
          action="../../controllers/adminUserController.php"
          data-ajax="true"
          data-reset="true"
          data-refresh-target="#usersTableBody"
          data-refresh-url="manage_users.php?partial=user_rows">

        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">

        <div class="form-row">
            <div class="input-group">
                <label>Username</label>
                <input type="text" name="username" class="form-control" placeholder="e.g. John Doe" required>
            </div>

            <div class="input-group">
                <label>Email Address</label>
                <input type="email" name="email" class="form-control" placeholder="user@example.com" required>
            </div>
        </div>

        <div class="form-row">
            <div class="input-group">
                <label>Password</label>
                <input type="password" name="password" class="form-control" placeholder="******" required>
            </div>

            <div class="input-group">
                <label>Role</label>
                <select name="role" class="form-control">
                    <option value="customer">Customer</option>
                    <option value="admin">Admin</option>
                </select>
            </div>
        </div>

        <button type="submit" name="add_user_btn" class="btn btn-primary">
            <i data-lucide="user-plus"></i> Create User
        </button>
    </form>
</div>

<div class="card" style="padding:0; overflow:hidden; margin-top:20px;">

    <div style="padding:15px; background:#f8fafc; border-bottom:1px solid #e2e8f0; display:flex; justify-content:space-between; align-items:center;">
        <h4 style="margin:0;">User List</h4>

        <div style="position:relative;">
            <i data-lucide="search" style="position:absolute; left:10px; top:10px; width:18px; color:#94a3b8;"></i>
            <input type="text" id="userSearchInput" class="form-control" placeholder="Search Name or Email..." onkeyup="filterUsers()" style="width:300px; padding-left:35px;">
        </div>
    </div>

    <table id="usersTable">
        <thead>
            <tr>
                <th>ID</th>
                <th>User Profile</th>
                <th>Email</th>
                <th>Role</th>
                <th>Action</th>
            </tr>
        </thead>

        <tbody id="usersTableBody">
            <?php foreach($users as $u){ ?>
            <tr>
                <td>#<?= $u['id'] ?></td>
                <td>
                    <div style="display:flex; align-items:center; gap:10px;">
                        <div style="width:32px; height:32px; background:#e2e8f0; border-radius:50%; display:flex; align-items:center; justify-content:center; font-weight:bold; color:#64748b;">
                            <?= strtoupper(substr($u['username'], 0, 1)) ?>
                        </div>
                        <b><?= htmlspecialchars($u['username']) ?></b>
                    </div>
                </td>
                <td><?= htmlspecialchars($u['email']) ?></td>
                <td>
                    <?php if (strtolower(trim($u['role'] ?? '')) === 'admin') { ?>
                        <span style="background:#dbeafe; color:#1e40af; padding:2px 8px; border-radius:4px; font-size:0.8rem; font-weight:bold;">Admin</span>
                    <?php } else { ?>
                        <span style="background:#f1f5f9; color:#475569; padding:2px 8px; border-radius:4px; font-size:0.8rem;">Customer</span>
                    <?php } ?>
                </td>
                <td>
                    <a href="edit_user.php?id=<?= $u['id'] ?>" class="btn btn-secondary" style="padding:5px 10px; font-size:0.8rem;">Edit</a>
                    <a
                      href="../../controllers/adminUserController.php?delete=<?= $u['id'] ?>"
                      class="btn btn-danger"
                      style="padding:5px 10px; font-size:0.8rem;"
                      data-ajax-link="true"
                      data-confirm="true"
                      data-confirm-text="Delete this user?"
                      data-refresh-target="#usersTableBody"
                      data-refresh-url="manage_users.php?partial=user_rows"
                    >Del</a>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<?php require_once('footer.php'); ?>

<script>
function filterUsers() {
    var input = document.getElementById("userSearchInput");
    var filter = input.value.toUpperCase();
    var table = document.getElementById("usersTable");
    var tr = table.getElementsByTagName("tr");

    for (var i = 1; i < tr.length; i++) {
        var tdName = tr[i].getElementsByTagName("td")[1];
        var tdEmail = tr[i].getElementsByTagName("td")[2];

        if (tdName || tdEmail) {
            var txtName = tdName.textContent || tdName.innerText;
            var txtEmail = tdEmail.textContent || tdEmail.innerText;

            if (txtName.toUpperCase().indexOf(filter) > -1 || txtEmail.toUpperCase().indexOf(filter) > -1) {
                tr[i].style.display = "";
            } else {
                tr[i].style.display = "none";
            }
        }
    }
}
</script>
