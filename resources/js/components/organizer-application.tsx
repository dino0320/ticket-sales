import {
  Card,
  CardContent,
  CardDescription,
  CardFooter,
  CardHeader,
  CardTitle,
} from '@/components/ui/card'

export type OrganizerApplicationData = {
  id: number,
  user_id: number,
  event_description: string,
  is_individual: boolean,
  website_url: string | null,
  applied_at: string,
}

export function OrganizerApplication({ userOrganizerApplication, isEllipsis = false }: { userOrganizerApplication: OrganizerApplicationData, isEllipsis?: boolean }) {
  return (
    <Card>
      <CardHeader>
        <CardTitle>User ID: {userOrganizerApplication.user_id}</CardTitle>
        <CardDescription className={isEllipsis ? 'overflow-ellipsis' : ''}>
          {userOrganizerApplication.event_description}
        </CardDescription>
      </CardHeader>
      <CardContent>
        {userOrganizerApplication.is_individual ? 'Individual' : 'Company'}
      </CardContent>
      <CardFooter>
        {userOrganizerApplication.applied_at}
      </CardFooter>
    </Card>
  )
}
