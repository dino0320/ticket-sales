import AccountController from './AccountController'
import OrganizerApplicationController from './OrganizerApplicationController'

const Admin = {
    AccountController: Object.assign(AccountController, AccountController),
    OrganizerApplicationController: Object.assign(OrganizerApplicationController, OrganizerApplicationController),
}

export default Admin