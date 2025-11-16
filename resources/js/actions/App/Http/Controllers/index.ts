import SignUpController from './SignUpController'
import SignInController from './SignInController'
import HomeController from './HomeController'
import TicketController from './TicketController'
import CartController from './CartController'
import CheckoutController from './CheckoutController'

const Controllers = {
    SignUpController: Object.assign(SignUpController, SignUpController),
    SignInController: Object.assign(SignInController, SignInController),
    HomeController: Object.assign(HomeController, HomeController),
    TicketController: Object.assign(TicketController, TicketController),
    CartController: Object.assign(CartController, CartController),
    CheckoutController: Object.assign(CheckoutController, CheckoutController),
}

export default Controllers