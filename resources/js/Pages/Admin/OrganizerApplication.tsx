import { show } from '@/actions/App/Http/Controllers/Admin/OrganizerApplicationController';
import { Link } from '@inertiajs/react'
import { Pagination } from '@/components/pagination'
import type { PaginationData } from '@/components/pagination'
import { OrganizerApplication as OrganizerApplicationComponent } from '@/components/organizer-application'
import type { OrganizerApplicationData } from '@/components/organizer-application'

export default function OrganizerApplication({ userOrganizerApplications }: { userOrganizerApplications: PaginationData<OrganizerApplicationData>}) {
  return (
    <div className="space-y-1">
      {userOrganizerApplications.data.map((userOrganizerApplication) => (
        <div key={userOrganizerApplication.id}>
          <Link href={show(userOrganizerApplication.id)}>
            <OrganizerApplicationComponent userOrganizerApplication={userOrganizerApplication} isEllipsis={true}/>
          </Link>
        </div>
      ))}

      <Pagination pagination={userOrganizerApplications}/>
    </div>
  )
}
