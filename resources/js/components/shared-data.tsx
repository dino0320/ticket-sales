export type AuthData = {
  auth: {
    user: {
      name: string,
      is_organizer: boolean,
    },
    is_admin: boolean,
  }
}