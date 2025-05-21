<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

// Fetch admin username and email from database
try {
    $stmt = $pdo->prepare('SELECT username, email FROM users WHERE id = :id AND role = :role');
    $stmt->execute(['id' => $_SESSION['user_id'], 'role' => 'admin']);
    $admin = $stmt->fetch();
    if ($admin) {
        $admin_username = htmlspecialchars($admin['username']);
        $admin_email = htmlspecialchars($admin['email']);
    } else {
        $admin_username = 'Admin';
        $admin_email = '';
    }
} catch (Exception $e) {
    $admin_username = 'Admin';
    $admin_email = '';
}

try {
    $stmtApps = $pdo->prepare("SELECT id, user_id, tradename, store_premises, store_location, ownership, company_name, business_address, tin, office_tel, tenant_representative, contact_person, position, contact_tel, mobile, email, prepared_by, business_type, documents, created_at FROM tenant_details ORDER BY created_at DESC");
    $stmtApps->execute();
    $applications = $stmtApps->fetchAll();
} catch (Exception $e) {
    $applications = [];
}

try {
    $stmtMaint = $pdo->prepare("SELECT permit_no, date_filed, tenant_name, scope_of_work, security_posting, rate_security, charge_security, janitorial_deployment, rate_janitorial, charge_janitorial, maintenance, rate_maintenance, charge_maintenance, personnel, created_at FROM work_permits ORDER BY created_at DESC");
    $stmtMaint->execute();
    $maintenanceRequests = $stmtMaint->fetchAll();
} catch (Exception $e) {
    $maintenanceRequests = [];
}

// Fetch renewal requests with tenant username
try {
    $stmtRenewal = $pdo->prepare("SELECT rr.id, rr.tenant_id, rr.renewal_date, rr.submitted_at, u.username FROM renewal_requests rr JOIN users u ON rr.tenant_id = u.id ORDER BY rr.submitted_at DESC");
    $stmtRenewal->execute();
    $renewalRequests = $stmtRenewal->fetchAll();
} catch (Exception $e) {
    $renewalRequests = [];
}

// Fetch payments with username
try {
    $stmtPayments = $pdo->prepare("SELECT p.id, p.user_id, p.payment_image, p.payment_date, p.status, u.username FROM payments p JOIN users u ON p.user_id = u.id ORDER BY p.payment_date DESC");
    $stmtPayments->execute();
    $payments = $stmtPayments->fetchAll();
} catch (Exception $e) {
    $payments = [];
}
?>
<!DOCTYPE html>
<html lang="en">
 <head>
  <meta charset="utf-8"/>
  <meta content="width=device-width, initial-scale=1" name="viewport"/>
  <title>
   Admin Dashboard
  </title>
  <script src="https://cdn.tailwindcss.com">
  </script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&amp;display=swap" rel="stylesheet"/>
  <style>
   body {
      font-family: "Poppins", sans-serif;
    }
  </style>
 </head>
 <body class="bg-[#f0f3f8] min-h-screen p-6">
  <div class="flex gap-8 max-w-[1400px] mx-auto">
   <!-- Sidebar -->
   <aside class="bg-[#e3e8ef] rounded-2xl w-64 p-6 flex flex-col gap-8 shadow-lg" style="box-shadow: 0 8px 15px rgb(0 0 0 / 0.05)">
    <div class="flex items-center gap-3">
     <div class="bg-[#5a21d7] p-3 rounded-md text-white">
      <i class="fas fa-wallet fa-lg">
      </i>
     </div>
     <h1 class="font-extrabold text-xl select-none">
      Admin
     </h1>
     <button aria-label="Toggle menu" class="ml-auto text-2xl text-black/80 hover:text-black">
      <i class="fas fa-bars">
      </i>
     </button>
    </div>
     <div class="flex items-center gap-4 bg-[#f0f3f8] rounded-xl p-4 border border-[#d9d9d9]">
      <div class="rounded-full bg-[#5a21d7] text-white p-3">
       <i class="fas fa-user fa-lg"></i>
      </div>
      <div class="text-sm text-gray-500">
       <p class="font-bold text-black">
        Welcome,
        <span class="select-none">
         <?php echo htmlspecialchars($admin_username); ?>
        </span>
       </p>
       <p class="select-none">
        <?php echo htmlspecialchars($admin_email); ?>
       </p>
      </div>
     </div>
    <nav class="flex flex-col gap-6 text-gray-400 select-none">
     <a class="flex items-center gap-3 hover:text-black transition cursor-pointer" id="viewDashboardLink">
      <i class="fas fa-tachometer-alt"></i>
      <span>Dashboard</span>
     </a>
     <a class="flex items-center gap-3 hover:text-black transition cursor-pointer" id="viewApplicationsLink">
      <i class="fas fa-file-alt"></i>
      <span>View Applications</span>
     </a>
     <a class="flex items-center gap-3 hover:text-black transition cursor-pointer" id="viewMaintenanceLink">
      <i class="fas fa-tools"></i>
      <span>View Maintenance Requests</span>
     </a>
     <a class="flex items-center gap-3 hover:text-black transition cursor-pointer" id="viewRenewalLink">
      <i class="fas fa-sync-alt"></i>
      <span>View Renewal Requests</span>
     </a>
     <a class="flex items-center gap-3 hover:text-black transition cursor-pointer" id="viewPaymentsLink">
      <i class="fas fa-credit-card"></i>
      <span>View Payments</span>
     </a>
     <a class="flex items-center gap-3 hover:text-black transition" href="post_announcements.php">
      <i class="fas fa-bullhorn"></i>
      <span> Post Announcements</span>
     </a>
     <a class="flex items-center gap-3 hover:text-black transition" href="logout.php">
      <i class="fas fa-sign-out-alt"></i>
      <span>Logout</span>
     </a>
    </nav>
   </aside>
   <!-- Main content -->
   <main class="flex-1 flex flex-col gap-6 overflow-auto scrollbar-thin" style="max-height: 90vh;">
    <header class="flex items-center justify-between">
     <h2 class="font-extrabold text-2xl select-none" id="sectionTitle">
      Dashboard
     </h2>
    </header>

    <section id="dashboardSection" class="space-y-6">
      <div class="flex flex-wrap gap-6">
       <div class="flex-1 min-w-[220px] max-w-[280px] bg-gradient-to-tr from-[#4b1dbb] to-[#5a21d7] rounded-2xl p-6 text-white shadow-lg relative overflow-hidden">
        <div class="absolute top-0 left-0 w-24 h-24 bg-gradient-to-tr from-[#6a2de7] to-[#4b1dbb] rounded-full opacity-30 -translate-x-12 -translate-y-12">
        </div>
        <div class="flex items-center gap-3 mb-4">
         <div class="bg-white/30 rounded-full p-3">
          <i class="fas fa-file-alt text-xl">
          </i>
         </div>
         <div class="text-2xl font-semibold select-none">
          2478
         </div>
        </div>
        <p class="text-sm select-none">
         Total Tenants
        </p>
       </div>
       <div class="flex-1 min-w-[220px] max-w-[280px] bg-gradient-to-tr from-[#a01ef7] to-[#d01aff] rounded-2xl p-6 text-white shadow-lg relative overflow-hidden">
        <div class="absolute top-0 left-0 w-24 h-24 bg-gradient-to-tr from-[#d01aff] to-[#a01ef7] rounded-full opacity-30 -translate-x-12 -translate-y-12">
        </div>
        <div class="flex items-center gap-3 mb-4">
         <div class="bg-white/30 rounded-full p-3">
          <i class="fas fa-check-circle text-xl">
          </i>
         </div>
         <div class="text-2xl font-semibold select-none">
          983
         </div>
        </div>
        <p class="text-sm select-none">
         Paid Tenants
        </p>
       </div>
       <div class="flex-1 min-w-[220px] max-w-[280px] bg-gradient-to-tr from-[#4b1dbb] to-[#5a21d7] rounded-2xl p-6 text-white shadow-lg relative overflow-hidden">
        <div class="absolute top-0 left-0 w-24 h-24 bg-gradient-to-tr from-[#6a2de7] to-[#4b1dbb] rounded-full opacity-30 -translate-x-12 -translate-y-12">
        </div>
        <div class="flex items-center gap-3 mb-4">
         <div class="bg-white/30 rounded-full p-3">
          <i class="fas fa-times-circle text-xl">
          </i>
         </div>
         <div class="text-2xl font-semibold select-none">
          1256
         </div>
        </div>
        <p class="text-sm select-none">
         Unpaid Tenants
        </p>
       </div>
       <div class="flex-1 min-w-[220px] max-w-[280px] bg-gradient-to-tr from-[#d01aff] to-[#4b1dbb] rounded-2xl p-6 text-white shadow-lg relative overflow-hidden">
        <div class="absolute top-0 left-0 w-24 h-24 bg-gradient-to-tr from-[#d01aff] to-[#4b1dbb] rounded-full opacity-30 -translate-x-12 -translate-y-12">
        </div>
        <div class="flex items-center gap-3 mb-4">
         <div class="bg-white/30 rounded-full p-3">
          <i class="fas fa-file-alt text-xl">
          </i>
         </div>
         <div class="text-2xl font-semibold select-none">
          652
         </div>
        </div>
        <p class="text-sm select-none">
         Overall tenants Paid
        </p>
       </div>
      </div>
    </section>

    <section id="applicationsSection" class="hidden space-y-6 overflow-auto scrollbar-thin" style="max-height: 80vh;">
      <h1 class="font-bold text-2xl mb-4">Application Submissions</h1>
      <table class="min-w-full bg-white border border-gray-300 rounded shadow">
        <thead>
          <tr class="bg-green-600 text-white">
            <th class="border px-4 py-2">ID</th>
            <th class="border px-4 py-2">User ID</th>
            <th class="border px-4 py-2">Trade Name</th>
            <th class="border px-4 py-2">Store Premises</th>
            <th class="border px-4 py-2">Store Location</th>
            <th class="border px-4 py-2">Ownership</th>
            <th class="border px-4 py-2">Company Name</th>
            <th class="border px-4 py-2">Business Address</th>
            <th class="border px-4 py-2">TIN</th>
            <th class="border px-4 py-2">Office Tel</th>
            <th class="border px-4 py-2">Tenant Representative</th>
            <th class="border px-4 py-2">Contact Person</th>
            <th class="border px-4 py-2">Position</th>
            <th class="border px-4 py-2">Contact Tel</th>
            <th class="border px-4 py-2">Mobile</th>
            <th class="border px-4 py-2">Email</th>
            <th class="border px-4 py-2">Prepared By</th>
            <th class="border px-4 py-2">Business Type</th>
            <th class="border px-4 py-2">Created At</th>
            <th class="border px-4 py-2">Remarks</th>
            <th class="border px-4 py-2">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($applications): ?>
            <?php foreach ($applications as $app): ?>
              <tr class="hover:bg-green-50">
                <td class="border px-4 py-2"><?php echo htmlspecialchars($app['id']); ?></td>
                <td class="border px-4 py-2"><?php echo htmlspecialchars($app['user_id']); ?></td>
                <td class="border px-4 py-2"><?php echo htmlspecialchars($app['tradename']); ?></td>
                <td class="border px-4 py-2"><?php echo htmlspecialchars($app['store_premises']); ?></td>
                <td class="border px-4 py-2"><?php echo htmlspecialchars($app['store_location']); ?></td>
                <td class="border px-4 py-2"><?php echo htmlspecialchars($app['ownership']); ?></td>
                <td class="border px-4 py-2"><?php echo htmlspecialchars($app['company_name']); ?></td>
                <td class="border px-4 py-2"><?php echo htmlspecialchars($app['business_address']); ?></td>
                <td class="border px-4 py-2"><?php echo htmlspecialchars($app['tin']); ?></td>
                <td class="border px-4 py-2"><?php echo htmlspecialchars($app['office_tel']); ?></td>
                <td class="border px-4 py-2"><?php echo htmlspecialchars($app['tenant_representative']); ?></td>
                <td class="border px-4 py-2"><?php echo htmlspecialchars($app['contact_person']); ?></td>
                <td class="border px-4 py-2"><?php echo htmlspecialchars($app['position']); ?></td>
                <td class="border px-4 py-2"><?php echo htmlspecialchars($app['contact_tel']); ?></td>
                <td class="border px-4 py-2"><?php echo htmlspecialchars($app['mobile']); ?></td>
                <td class="border px-4 py-2"><?php echo htmlspecialchars($app['email']); ?></td>
                <td class="border px-4 py-2"><?php echo htmlspecialchars($app['prepared_by']); ?></td>
                <td class="border px-4 py-2"><?php echo htmlspecialchars($app['business_type']); ?></td>
                <td class="border px-4 py-2"><?php echo htmlspecialchars($app['created_at']); ?></td>
                <td class="border px-4 py-2 flex gap-2 justify-center">
                  <button class="approveBtn bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition" data-id="<?php echo htmlspecialchars($app['id']); ?>">Approve</button>
                  <button class="declineBtn bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 transition" data-id="<?php echo htmlspecialchars($app['id']); ?>">Decline</button>
                </td>
                <td class="border px-4 py-2">
                  <?php if (!empty($app['documents'])): ?>
                    <a href="<?php echo htmlspecialchars($app['documents']); ?>" target="_blank" class="text-blue-600 underline">View Documents</a>
                  <?php else: ?>
                    No Documents
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr><td colspan="21" class="border px-4 py-2 text-center">No application submissions found.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </section>

    <section id="maintenanceSection" class="hidden space-y-6 overflow-auto scrollbar-thin" style="max-height: 80vh;">
      <h1 class="font-bold text-2xl mb-4">Work Permit</h1>

      <div class="bg-white p-6 rounded-lg shadow-md border border-green-600 mb-6 max-w-3xl">
        <form method="POST" action="maintenance_request.php" class="space-y-4">
          <div>
            <label for="maintenance_details" class="block font-semibold mb-1 text-green-700">Maintenance Details</label>
            <textarea id="maintenance_details" name="maintenance_details" required class="w-full p-2 border border-green-600 rounded"></textarea>
          </div>
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label for="date_from" class="block font-semibold mb-1 text-green-700">Permit Valid From</label>
              <input type="date" id="date_from" name="date_from" required class="w-full p-2 border border-green-600 rounded" />
            </div>
            <div>
              <label for="date_to" class="block font-semibold mb-1 text-green-700">Permit Valid To</label>
              <input type="date" id="date_to" name="date_to" required class="w-full p-2 border border-green-600 rounded" />
            </div>
          </div>
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label for="time_from" class="block font-semibold mb-1 text-green-700">Time From</label>
              <input type="time" id="time_from" name="time_from" required class="w-full p-2 border border-green-600 rounded" />
            </div>
            <div>
              <label for="time_to" class="block font-semibold mb-1 text-green-700">Time To</label>
              <input type="time" id="time_to" name="time_to" required class="w-full p-2 border border-green-600 rounded" />
            </div>
          </div>
          <div>
            <label for="tenant_email" class="block font-semibold mb-1 text-green-700">Tenant Email</label>
            <input type="email" id="tenant_email" name="tenant_email" required class="w-full p-2 border border-green-600 rounded" />
          </div>
          <div>
            <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded hover:bg-green-700 transition">Submit</button>
          </div>
        </form>
      </div>

      <table class="min-w-full bg-white border border-gray-300 rounded shadow">
        <thead>
          <tr class="bg-green-600 text-white">
            <th class="border px-4 py-2">Permit No</th>
            <th class="border px-4 py-2">Date Filed</th>
            <th class="border px-4 py-2">Tenant Name</th>
            <th class="border px-4 py-2">Scope of Work</th>
            <th class="border px-4 py-2">Security Posting</th>
            <th class="border px-4 py-2">Rate Security</th>
            <th class="border px-4 py-2">Charge Security</th>
            <th class="border px-4 py-2">Janitorial Deployment</th>
            <th class="border px-4 py-2">Rate Janitorial</th>
            <th class="border px-4 py-2">Charge Janitorial</th>
            <th class="border px-4 py-2">Maintenance</th>
            <th class="border px-4 py-2">Rate Maintenance</th>
            <th class="border px-4 py-2">Charge Maintenance</th>
            <th class="border px-4 py-2">Personnel</th>
            <th class="border px-4 py-2">Created At</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($maintenanceRequests): ?>
            <?php foreach ($maintenanceRequests as $req): ?>
              <tr class="hover:bg-green-50">
                <td class="border px-4 py-2"><?php echo htmlspecialchars($req['permit_no']); ?></td>
                <td class="border px-4 py-2"><?php echo htmlspecialchars($req['date_filed']); ?></td>
                <td class="border px-4 py-2"><?php echo htmlspecialchars($req['tenant_name']); ?></td>
                <td class="border px-4 py-2"><?php echo htmlspecialchars($req['scope_of_work']); ?></td>
                <td class="border px-4 py-2"><?php echo htmlspecialchars($req['security_posting']); ?></td>
                <td class="border px-4 py-2"><?php echo htmlspecialchars($req['rate_security']); ?></td>
                <td class="border px-4 py-2"><?php echo htmlspecialchars($req['charge_security']); ?></td>
                <td class="border px-4 py-2"><?php echo htmlspecialchars($req['janitorial_deployment']); ?></td>
                <td class="border px-4 py-2"><?php echo htmlspecialchars($req['rate_janitorial']); ?></td>
                <td class="border px-4 py-2"><?php echo htmlspecialchars($req['charge_janitorial']); ?></td>
                <td class="border px-4 py-2"><?php echo htmlspecialchars($req['maintenance']); ?></td>
                <td class="border px-4 py-2"><?php echo htmlspecialchars($req['rate_maintenance']); ?></td>
                <td class="border px-4 py-2"><?php echo htmlspecialchars($req['charge_maintenance']); ?></td>
                <td class="border px-4 py-2"><?php echo htmlspecialchars($req['personnel']); ?></td>
                <td class="border px-4 py-2"><?php echo htmlspecialchars($req['created_at']); ?></td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr><td colspan="15" class="border px-4 py-2 text-center">No maintenance requests found.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </section>

    <section id="renewalSection" class="hidden space-y-6 overflow-auto scrollbar-thin" style="max-height: 80vh;">
      <h1 class="font-bold text-2xl mb-4">Renewal Requests</h1>
      <table class="min-w-full bg-white border border-gray-300 rounded shadow">
        <thead>
          <tr class="bg-green-600 text-white">
            <th class="border px-4 py-2">ID</th>
            <th class="border px-4 py-2">Tenant Username</th>
            <th class="border px-4 py-2">Renewal Date</th>
            <th class="border px-4 py-2">Submitted At</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($renewalRequests): ?>
            <?php foreach ($renewalRequests as $req): ?>
              <tr class="hover:bg-green-50">
                <td class="border px-4 py-2"><?php echo htmlspecialchars($req['id']); ?></td>
                <td class="border px-4 py-2"><?php echo htmlspecialchars($req['username']); ?></td>
                <td class="border px-4 py-2"><?php echo htmlspecialchars($req['renewal_date']); ?></td>
                <td class="border px-4 py-2"><?php echo htmlspecialchars($req['submitted_at']); ?></td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr><td colspan="4" class="border px-4 py-2 text-center">No renewal requests found.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </section>

    <section id="paymentsSection" class="hidden space-y-6 overflow-auto scrollbar-thin" style="max-height: 80vh;">
      <h1 class="font-bold text-2xl mb-4">Payments</h1>
      <table class="min-w-full bg-white border border-gray-300 rounded shadow">
        <thead>
          <tr class="bg-green-600 text-white">
            <th class="border px-4 py-2">ID</th>
            <th class="border px-4 py-2">Tenant Username</th>
            <th class="border px-4 py-2">Payment Image</th>
            <th class="border px-4 py-2">Payment Date</th>
            <th class="border px-4 py-2">Status</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($payments): ?>
            <?php foreach ($payments as $pay): ?>
              <tr class="hover:bg-green-50">
                <td class="border px-4 py-2"><?php echo htmlspecialchars($pay['id']); ?></td>
                <td class="border px-4 py-2"><?php echo htmlspecialchars($pay['username']); ?></td>
                <td class="border px-4 py-2"><a href="<?php echo htmlspecialchars($pay['payment_image']); ?>" target="_blank" class="text-blue-600 underline">View Image</a></td>
                <td class="border px-4 py-2"><?php echo htmlspecialchars($pay['payment_date']); ?></td>
                <td class="border px-4 py-2"><?php echo htmlspecialchars(ucfirst($pay['status'])); ?></td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr><td colspan="5" class="border px-4 py-2 text-center">No payments found.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </section>

  </main>
  </div>

  <!-- Modal for admin feedback -->
  <div id="feedbackModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
    <div class="bg-white rounded-lg p-6 w-96">
      <h2 id="modalTitle" class="text-xl font-bold mb-4">Provide Feedback</h2>
      <form id="feedbackForm">
        <input type="hidden" id="applicationId" name="applicationId" />
        <input type="hidden" id="actionType" name="actionType" />
        <div class="mb-4">
          <label for="feedbackText" class="block mb-1 font-semibold">Feedback</label>
          <textarea id="feedbackText" name="feedbackText" rows="4" class="w-full border border-gray-300 rounded p-2" required></textarea>
        </div>
        <div class="flex justify-end gap-4">
          <button type="button" id="cancelBtn" class="px-4 py-2 rounded bg-gray-300 hover:bg-gray-400">Cancel</button>
          <button type="submit" class="px-4 py-2 rounded bg-blue-600 text-white hover:bg-blue-700">Send</button>
        </div>
      </form>
    </div>
  </div>

  <script>
    const viewDashboardLink = document.getElementById('viewDashboardLink');
    const viewApplicationsLink = document.getElementById('viewApplicationsLink');
    const viewMaintenanceLink = document.getElementById('viewMaintenanceLink');
    const viewRenewalLink = document.getElementById('viewRenewalLink');
    const viewPaymentsLink = document.getElementById('viewPaymentsLink');

    const dashboardSection = document.getElementById('dashboardSection');
    const applicationsSection = document.getElementById('applicationsSection');
    const maintenanceSection = document.getElementById('maintenanceSection');
    const renewalSection = document.getElementById('renewalSection');
    const paymentsSection = document.getElementById('paymentsSection');
    const sectionTitle = document.getElementById('sectionTitle');

    function hideAllSections() {
      dashboardSection.classList.add('hidden');
      applicationsSection.classList.add('hidden');
      maintenanceSection.classList.add('hidden');
      renewalSection.classList.add('hidden');
      paymentsSection.classList.add('hidden');
    }

    viewDashboardLink.addEventListener('click', () => {
      hideAllSections();
      dashboardSection.classList.remove('hidden');
      sectionTitle.textContent = 'Dashboard';
    });

    viewApplicationsLink.addEventListener('click', () => {
      hideAllSections();
      applicationsSection.classList.remove('hidden');
      sectionTitle.textContent = 'Application Submissions';
    });

    viewMaintenanceLink.addEventListener('click', () => {
      hideAllSections();
      maintenanceSection.classList.remove('hidden');
      sectionTitle.textContent = 'Maintenance Requests';
    });

    viewRenewalLink.addEventListener('click', () => {
      hideAllSections();
      renewalSection.classList.remove('hidden');
      sectionTitle.textContent = 'Renewal Requests';
    });

    viewPaymentsLink.addEventListener('click', () => {
      hideAllSections();
      paymentsSection.classList.remove('hidden');
      sectionTitle.textContent = 'Payments';
    });

    // Modal logic for feedback
    const feedbackModal = document.getElementById('feedbackModal');
    const feedbackForm = document.getElementById('feedbackForm');
    const modalTitle = document.getElementById('modalTitle');
    const applicationIdInput = document.getElementById('applicationId');
    const actionTypeInput = document.getElementById('actionType');
    const feedbackText = document.getElementById('feedbackText');
    const cancelBtn = document.getElementById('cancelBtn');

    // Open modal on approve/decline button click
    document.querySelectorAll('.approveBtn, .declineBtn').forEach(button => {
      button.addEventListener('click', () => {
        const appId = button.getAttribute('data-id');
        const action = button.classList.contains('approveBtn') ? 'Approve' : 'Decline';
        applicationIdInput.value = appId;
        actionTypeInput.value = action;
        modalTitle.textContent = `${action} Application - Provide Feedback`;
        feedbackText.value = '';
        feedbackModal.classList.remove('hidden');
      });
    });

    // Cancel button closes modal
    cancelBtn.addEventListener('click', () => {
      feedbackModal.classList.add('hidden');
    });

    // Handle form submission
    feedbackForm.addEventListener('submit', async (e) => {
      e.preventDefault();
      const formData = new FormData(feedbackForm);
      console.log('Submitting feedback form...');
      try {
        const response = await fetch('admin_application_review.php', {
          method: 'POST',
          body: formData
        });
        const result = await response.json();
        console.log('Response:', result);
        if (result.success) {
          alert('Feedback sent successfully.');
          feedbackModal.classList.add('hidden');
          // Optionally refresh or update the UI here
          location.reload();
        } else {
          alert('Error: ' + result.message);
        }
      } catch (error) {
        console.error('Error submitting feedback:', error);
        alert('An error occurred while sending feedback.');
      }
    });
  </script>
 </body>
</html>
