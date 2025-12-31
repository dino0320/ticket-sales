import AccountController from './AccountController'
import HomeController from './HomeController'
import TicketController from './TicketController'
import CartController from './CartController'
import CheckoutController from './CheckoutController'
import Admin from './Admin'

const Controllers = {
    AccountController: Object.assign(AccountController, AccountController),
    HomeController: Object.assign(HomeController, HomeController),
    TicketController: Object.assign(TicketController, TicketController),
    CartController: Object.assign(CartController, CartController),
    CheckoutController: Object.assign(CheckoutController, CheckoutController),
    Admin: Object.assign(Admin, Admin),
}

export default Controllers