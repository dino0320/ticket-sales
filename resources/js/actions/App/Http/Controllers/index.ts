import SignUpController from './SignUpController'
import SignInController from './SignInController'

const Controllers = {
    SignUpController: Object.assign(SignUpController, SignUpController),
    SignInController: Object.assign(SignInController, SignInController),
}

export default Controllers