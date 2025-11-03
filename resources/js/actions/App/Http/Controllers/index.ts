import SignUpController from './SignUpController'
import SignInController from './SignInController'
import HomeController from './HomeController'
import TicketController from './TicketController'

const Controllers = {
    SignUpController: Object.assign(SignUpController, SignUpController),
    SignInController: Object.assign(SignInController, SignInController),
    HomeController: Object.assign(HomeController, HomeController),
    TicketController: Object.assign(TicketController, TicketController),
}

export default Controllers