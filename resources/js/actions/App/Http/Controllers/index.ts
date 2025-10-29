import SignUpController from './SignUpController'
import SignInController from './SignInController'
import HomeController from './HomeController'

const Controllers = {
    SignUpController: Object.assign(SignUpController, SignUpController),
    SignInController: Object.assign(SignInController, SignInController),
    HomeController: Object.assign(HomeController, HomeController),
}

export default Controllers