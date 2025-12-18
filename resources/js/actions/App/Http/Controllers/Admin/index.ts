import SignInController from './SignInController'
import OrganizerApplicationController from './OrganizerApplicationController'

const Admin = {
    SignInController: Object.assign(SignInController, SignInController),
    OrganizerApplicationController: Object.assign(OrganizerApplicationController, OrganizerApplicationController),
}

export default Admin