import HomeController from './HomeController'
import CartController from './CartController'
import TicketController from './TicketController'
import AccountController from './AccountController'
import CheckoutController from './CheckoutController'
import Admin from './Admin'

const Controllers = {
    HomeController: Object.assign(HomeController, HomeController),
    CartController: Object.assign(CartController, CartController),
    TicketController: Object.assign(TicketController, TicketController),
    AccountController: Object.assign(AccountController, AccountController),
    CheckoutController: Object.assign(CheckoutController, CheckoutController),
    Admin: Object.assign(Admin, Admin),
}

export default Controllers