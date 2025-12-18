import SignUpController from './SignUpController'
import SignInController from './SignInController'
import HomeController from './HomeController'
import TicketController from './TicketController'
import CartController from './CartController'
import CheckoutController from './CheckoutController'
import AccountController from './AccountController'
import Admin from './Admin'

const Controllers = {
    SignUpController: Object.assign(SignUpController, SignUpController),
    SignInController: Object.assign(SignInController, SignInController),
    HomeController: Object.assign(HomeController, HomeController),
    TicketController: Object.assign(TicketController, TicketController),
    CartController: Object.assign(CartController, CartController),
    CheckoutController: Object.assign(CheckoutController, CheckoutController),
    AccountController: Object.assign(AccountController, AccountController),
    Admin: Object.assign(Admin, Admin),
}

export default Controllers