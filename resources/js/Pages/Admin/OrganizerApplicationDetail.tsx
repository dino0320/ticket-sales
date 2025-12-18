import { updateStatus } from '@/actions/App/Http/Controllers/Admin/OrganizerApplicationController';
import { router } from '@inertiajs/react'
import { Button } from '@/components/ui/button'
import { OrganizerApplication } from '@/components/organizer-application'
import type { OrganizerApplicationData } from '@/components/organizer-application'

export default function OrganizerApplicationDetail({ userOrganizerApplication }: { userOrganizerApplication: OrganizerApplicationData}) {
  async function onClick(isApproved: boolean) {
    try {
      router.put(updateStatus(userOrganizerApplication.id), { is_approved: isApproved }, { 
        onSuccess: () => console.log('The application status was updated'),
        onError: () => console.error('Can\'t update the application status'),
      })
    } catch (error) {
      console.error('Can\'t update the application status', error)
    }
  }

  return (
    <div>
      <OrganizerApplication userOrganizerApplication={userOrganizerApplication}/>
      <Button onClick={() => onClick(true)}>Approve</Button>
      <Button onClick={() => onClick(false)}>Reject</Button>
    </div>
  )
}
