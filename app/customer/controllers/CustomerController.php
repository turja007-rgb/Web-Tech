<?php
// app/customer/controllers/CustomerController.php
namespace App\Customer\Controllers;
use App\Customer\Models\Customer;
use App\Customer\Models\Order;
use App\Customer\Models\Product;
use Config\Response;
require_once dirname(__DIR__, 3) . '/config/helpers.php';

class CustomerController {
    private Customer $customers;
    protected ?array $customer = null;
    private Order $orders;
    private array $cfg;

    public function __construct() {
        $this->customers = new Customer();
        $this->orders = new Order();
        $this->cfg = require dirname(__DIR__, 3) . '/config/config.php';
    }

    // -------- helpers ----------
    private function authId(): ?int { return $_SESSION['customer_id'] ?? null; }
    protected function requireAuth(): int {
        $id = $this->authId();
        if (!$id) Response::redirect(url('/login'));
        return $id;
    }
    private function setRememberCookie(int $userId): void {
        $exp = time() + 60*60*24*10; // 10 days
        $sig = hash_hmac('sha256', $userId . '|' . $exp, $this->cfg['secret_key']);
        $val = $userId . '|' . $exp . '|' . $sig;
        setcookie(
            'remember_me', $val, [
                'expires'  => $exp,
                'path'     => '/',
                'secure'   => (bool)$this->cfg['cookie_secure'],
                'httponly' => true,
                'samesite' => 'Lax',
            ]
        );
    }
    private function clearRememberCookie(): void {
        setcookie('remember_me','',[
            'expires'=>time()-3600,'path'=>'/','secure'=>$this->cfg['cookie_secure'],
            'httponly'=>true,'samesite'=>'Lax'
        ]);
    }
    // ---------------------------

    public function home(): void
    {
//        $logged_in = !empty($_SESSION['customer_id']);
        $productModel = new Product();
        $products = $productModel->getAllProducts();
        // For now a simple landing using the view header/footer
        Response::view(dirname(__DIR__,3).'/app/customer/views/index.php', [
            'authId' => $this->authId(),
            'title' => 'Welcome to the Bakery',
            'products' => $products
        ]);
    }

    public function showLogin(): void
    {
        if ($this->authId()) Response::redirect('/profile');
        $errors = Response::getFlash('errors', []);
        $old = Response::getFlash('old', []);
        Response::view(ROOT.'/app/customer/views/login.php', compact('errors','old'));
    }

    public function login() : void{
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $remember = !empty($_POST['remember']);

        $errors = [];
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors['email'] = 'Valid email required';
        if ($password === '') $errors['password'] = 'Password required';

        if ($errors) {
            Response::setFlash('errors', $errors);
            Response::setFlash('old', ['email'=>$email]);
            Response::redirect(url('/login'));
        }

        $user = $this->customers->findByEmail($email);
        if (!$user || !password_verify($password, $user['password_hash'])) {
            Response::setFlash('errors', ['general'=>'Invalid credentials']);
            Response::setFlash('old', ['email'=>$email]);
            Response::redirect(url('/login'));
        }

        $_SESSION['customer_id'] = (int)$user['id'];
        if ($remember) $this->setRememberCookie((int)$user['id']);

        Response::redirect(url('/profile'));
    }

    public function showRegister() : void{
        if ($this->authId()) Response::redirect(url('/profile'));
        $errors = Response::getFlash('errors', []);
        $old = Response::getFlash('old', []);
        Response::view(ROOT.'/app/customer/views/register.php', compact('errors','old'));
    }

    public function register() : void{
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm  = $_POST['password_confirmation'] ?? '';

        $errors = [];

        if ($name === '' || mb_strlen($name) < 2) $errors['name'] = 'Name must be at least 2 characters';
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors['email'] = 'Valid email required';
        if ($phone !== '' && !preg_match('/^[0-9+\-\s]{6,20}$/', $phone)) $errors['phone'] = 'Invalid phone';
        if (strlen($password) < 6) $errors['password'] = 'Password must be at least 6 characters';
        if ($password !== $confirm) $errors['password_confirmation'] = 'Passwords not match';

        if ($this->customers->findByEmail($email)) {
            $errors['email'] = 'Email already registered';
        }

        if ($errors) {
            Response::setFlash('errors', $errors);
            Response::setFlash('old', compact('name','email','phone'));
            Response::redirect(url('/register'));
        }

        $uid = $this->customers->create([
            'name'=>$name,'email'=>$email,'phone'=>$phone,'password'=>$password
        ]);

        $_SESSION['customer_id'] = $uid;
        $this->setRememberCookie($uid); // keep them logged in
        Response::redirect(url('/profile'));
    }

    public function logout() : void{
        unset($_SESSION['customer_id']);
        $this->clearRememberCookie();
        Response::redirect(url('/'));
    }

    public function profile() : void {
        $id = $this->requireAuth();
        $customer = $this->customers->findById($id);
        //Fetch Orders for this Customer
        $orderHistory = $this->orders->getCustomerOrderHistory($id);
        Response::view(dirname(__DIR__,3).'/app/customer/views/profile.php', compact('customer', 'orderHistory'));
    }
    //Profile Update
    public function profileUpdate(): void {
        $id = $this->requireAuth();
        header('Content-Type: application/json');

        $customer = $this->customers->findById($id);
        if (!$customer) {
            echo json_encode(['status'=>'error', 'message'=>'Customer not found']);
            exit;
        }

        $name = trim($_POST['name'] ?? $customer['name']);
        $email = trim($_POST['email'] ?? $customer['email']);
        $phone = trim($_POST['phone'] ?? $customer['phone']);
        $newPassword = trim($_POST['password'] ?? '');
        $currentPassword = trim($_POST['current_password'] ?? '');

        $errors = [];

        // Verify current password
        if (!password_verify($currentPassword, $customer['password_hash'])) {
            $errors['current_password'] = 'Current password is incorrect';
        }

        if ($name === '' || mb_strlen($name) < 2) $errors['name'] = 'Name must be at least 2 characters';
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors['email'] = 'Valid email required';
        if ($phone !== '' && !preg_match('/^[0-9+\-\s]{6,20}$/', $phone)) $errors['phone'] = 'Invalid phone';
        if ($newPassword !== '' && strlen($newPassword) < 6) $errors['password'] = 'Password must be at least 6 characters';

        if ($errors) {
            echo json_encode(['status'=>'error','errors'=>$errors]);
            exit;
        }

        $updateData = ['name'=>$name,'email'=>$email,'phone'=>$phone];
        if ($newPassword !== '') $updateData['password'] = $newPassword;

        $this->customers->update($id, $updateData);

        echo json_encode([
            'status'=>'success',
            'message'=>'Profile updated successfully',
            'updatedFields' => $updateData
        ]);
    }
    public function orderHistory() : void {
        $id = $this->requireAuth();
        $orderHistory = $this->orders->getCustomerOrderHistory($id);
        Response::view(dirname(__DIR__,3).'/app/customer/views/order_history.php', compact('orderHistory'));
    }
    //Get Customer Data
    public function getCustomer($id) : ?array
    {
        $customerData = $this->customers->findById($id);
        return $customerData;
    }
}
