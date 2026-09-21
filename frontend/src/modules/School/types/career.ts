export type JobType = 'fulltime' | 'parttime' | 'internship' | 'freelance' | 'contract';

export type WorkplaceType = 'on_site' | 'remote' | 'hybrid';

export type ApplicationStatus = 'submitted' | 'reviewed' | 'shortlisted' | 'interview' | 'accepted' | 'rejected';

export interface JobVacancyData {
    id: number;
    school_id: number;
    department_id?: number | null;
    company_name: string;
    company_logo_url?: string | null;
    job_title: string;
    job_type: JobType;
    workplace_type: WorkplaceType;
    location?: string | null;
    salary_range?: string | null;
    description: string;
    requirements?: string | null;
    benefits?: string | null;
    application_deadline?: string | null;
    contact_email?: string | null;
    contact_phone?: string | null;
    external_url?: string | null;
    status: 'draft' | 'published' | 'closed';
    created_at?: string;
    updated_at?: string;
    applications_count?: number;
    department?: {
        id: number;
        code: string;
        name: string;
    } | null;
    applications?: JobApplicationData[];
}

export interface JobApplicationData {
    id: number;
    vacancy_id: number;
    student_id?: number | null;
    alumni_id?: number | null;
    applicant_name: string;
    applicant_email: string;
    applicant_phone: string;
    resume_url?: string | null;
    portfolio_url?: string | null;
    cover_letter?: string | null;
    status: ApplicationStatus;
    notes?: string | null;
    created_at?: string;
    student?: {
        id: number;
        full_name: string;
        nisn?: string | null;
        department?: {
            id: number;
            code: string;
            name: string;
        } | null;
    } | null;
    alumni?: {
        id: number;
        graduation_year: string;
        career_status: string;
    } | null;
}

export interface CareerStats {
    total_vacancies: number;
    active_vacancies: number;
    total_applications: number;
    accepted_candidates: number;
}
