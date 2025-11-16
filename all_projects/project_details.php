<?php
// Sample detailed project data (will replace with DB query results)
$project_id = isset($_GET['id']) ? (int)$_GET['id'] : 1;
$component = isset($_GET['component']) ? $_GET['component'] : 'CWTS';

// Sample project data based on component type and project ID
$project_data = [];

if ($component === 'ROTC') {
    if ($project_id == 1) {
        $project_data = [
            'id' => 1,
            'project_name' => 'Military Training Program',
            'team_name' => 'Team Alpha',
            'team_logo' => 'team_logo.jpg',
            'component' => 'ROTC',
            'nstp_section' => 'Section A',
            'submitted_date' => '2024-01-15',
            'status' => 'approved',
            'members' => [
                [
                    'name' => 'John Smith',
                    'role' => 'Cadet Commander',
                    'email' => 'john.smith@student.edu.ph',
                    'contact' => '09123456789'
                ],
                [
                    'name' => 'Sarah Johnson',
                    'role' => 'Training Officer',
                    'email' => 'sarah.johnson@student.edu.ph',
                    'contact' => '09123456790'
                ],
                [
                    'name' => 'Mike Wilson',
                    'role' => 'Logistics Officer',
                    'email' => 'mike.wilson@student.edu.ph',
                    'contact' => '09123456791'
                ]
            ],
            'issues' => 'The community lacks proper military training and discipline programs for youth development. Many young people need structured leadership training and physical fitness programs.',
            'objectives' => 'To provide comprehensive military training and leadership development programs for community youth, promoting discipline, physical fitness, and civic responsibility.',
            'target_community' => 'Youth aged 16-25 years old in the local community, particularly those interested in military service or leadership development.',
            'solutions' => 'Conduct regular military training sessions, leadership workshops, physical fitness programs, and community service activities.',
            'outcomes' => 'Improved discipline among youth, enhanced leadership skills, better physical fitness, increased civic responsibility, and potential military service candidates.',
            'activities' => [
                [
                    'stage' => 'Recruitment',
                    'activities' => 'Recruit participants and conduct orientation',
                    'timeframe' => 'Week 1-2',
                    'point_person' => 'John Smith',
                    'status' => 'Completed'
                ],
                [
                    'stage' => 'Training',
                    'activities' => 'Conduct military drills and leadership training',
                    'timeframe' => 'Week 3-8',
                    'point_person' => 'Sarah Johnson',
                    'status' => 'Ongoing'
                ],
                [
                    'stage' => 'Evaluation',
                    'activities' => 'Assess training effectiveness and graduation ceremony',
                    'timeframe' => 'Week 9-10',
                    'point_person' => 'Mike Wilson',
                    'status' => 'Planned'
                ]
            ],
            'budget' => [
                [
                    'activity' => 'Training Equipment',
                    'resources' => 'Military uniforms, training materials, equipment',
                    'partners' => 'Military supply store, ROTC unit',
                    'amount' => '₱ 8,000'
                ],
                [
                    'activity' => 'Transportation',
                    'resources' => 'Vehicle rental for field exercises',
                    'partners' => 'Local transport service',
                    'amount' => '₱ 3,000'
                ],
                [
                    'activity' => 'Meals and Refreshments',
                    'resources' => 'Food and drinks during training',
                    'partners' => 'Local catering service',
                    'amount' => '₱ 4,000'
                ]
            ]
        ];
    } elseif ($project_id == 2) {
        $project_data = [
            'id' => 2,
            'project_name' => 'Leadership Development Initiative',
            'team_name' => 'Team Bravo',
            'team_logo' => 'team_logo.jpg',
            'component' => 'ROTC',
            'nstp_section' => 'Section A',
            'submitted_date' => '2024-01-16',
            'status' => 'ongoing',
            'members' => [
                [
                    'name' => 'Alex Thompson',
                    'role' => 'Leadership Coordinator',
                    'email' => 'alex.thompson@student.edu.ph',
                    'contact' => '09123456789'
                ],
                [
                    'name' => 'Emma Davis',
                    'role' => 'Program Manager',
                    'email' => 'emma.davis@student.edu.ph',
                    'contact' => '09123456790'
                ],
                [
                    'name' => 'Ryan Brown',
                    'role' => 'Assessment Officer',
                    'email' => 'ryan.brown@student.edu.ph',
                    'contact' => '09123456791'
                ]
            ],
            'issues' => 'Young leaders in the community lack structured leadership development programs and mentorship opportunities. There is a need for comprehensive leadership training and skill development.',
            'objectives' => 'To develop and enhance leadership skills among community youth through structured training programs, mentorship, and practical leadership experiences.',
            'target_community' => 'Young adults aged 18-25 years old who show leadership potential and are interested in community service and development.',
            'solutions' => 'Conduct leadership workshops, provide mentorship programs, organize community service projects, and create leadership development opportunities.',
            'outcomes' => 'Enhanced leadership capabilities, increased community engagement, better decision-making skills, and development of future community leaders.',
            'activities' => [
                [
                    'stage' => 'Selection',
                    'activities' => 'Identify and select potential leaders',
                    'timeframe' => 'Week 1-2',
                    'point_person' => 'Alex Thompson',
                    'status' => 'Completed'
                ],
                [
                    'stage' => 'Training',
                    'activities' => 'Conduct leadership workshops and mentorship',
                    'timeframe' => 'Week 3-8',
                    'point_person' => 'Emma Davis',
                    'status' => 'Ongoing'
                ],
                [
                    'stage' => 'Application',
                    'activities' => 'Apply leadership skills in community projects',
                    'timeframe' => 'Week 9-10',
                    'point_person' => 'Ryan Brown',
                    'status' => 'Planned'
                ]
            ],
            'budget' => [
                [
                    'activity' => 'Training Materials',
                    'resources' => 'Leadership books, training materials, certificates',
                    'partners' => 'Educational foundation, leadership institute',
                    'amount' => '₱ 7,000'
                ],
                [
                    'activity' => 'Mentorship Program',
                    'resources' => 'Mentor stipends, meeting expenses',
                    'partners' => 'Local business leaders, community mentors',
                    'amount' => '₱ 5,000'
                ],
                [
                    'activity' => 'Community Projects',
                    'resources' => 'Project materials and implementation costs',
                    'partners' => 'Local government, community organizations',
                    'amount' => '₱ 8,000'
                ]
            ]
        ];
    } else { // project_id == 3
        $project_data = [
            'id' => 3,
            'project_name' => 'Community Defense Program',
            'team_name' => 'Team Charlie',
            'team_logo' => 'team_logo.jpg',
            'component' => 'ROTC',
            'nstp_section' => 'Section A',
            'submitted_date' => '2024-01-17',
            'status' => 'approved',
            'members' => [
                [
                    'name' => 'David Lee',
                    'role' => 'Defense Coordinator',
                    'email' => 'david.lee@student.edu.ph',
                    'contact' => '09123456789'
                ],
                [
                    'name' => 'Sophie Kim',
                    'role' => 'Security Specialist',
                    'email' => 'sophie.kim@student.edu.ph',
                    'contact' => '09123456790'
                ],
                [
                    'name' => 'James Park',
                    'role' => 'Community Liaison',
                    'email' => 'james.park@student.edu.ph',
                    'contact' => '09123456791'
                ]
            ],
            'issues' => 'The community lacks proper security awareness and emergency preparedness programs. Residents need training on basic defense strategies and emergency response procedures.',
            'objectives' => 'To enhance community security awareness and emergency preparedness through training programs, security assessments, and community defense initiatives.',
            'target_community' => 'All community residents, with special focus on neighborhood watch groups, local security personnel, and community leaders.',
            'solutions' => 'Conduct security awareness workshops, emergency preparedness training, community patrol programs, and establish neighborhood watch systems.',
            'outcomes' => 'Improved community security, better emergency response capabilities, enhanced neighborhood safety, and stronger community defense networks.',
            'activities' => [
                [
                    'stage' => 'Assessment',
                    'activities' => 'Assess community security needs and vulnerabilities',
                    'timeframe' => 'Week 1-2',
                    'point_person' => 'David Lee',
                    'status' => 'Completed'
                ],
                [
                    'stage' => 'Training',
                    'activities' => 'Conduct security and emergency preparedness training',
                    'timeframe' => 'Week 3-8',
                    'point_person' => 'Sophie Kim',
                    'status' => 'Ongoing'
                ],
                [
                    'stage' => 'Implementation',
                    'activities' => 'Establish neighborhood watch and patrol systems',
                    'timeframe' => 'Week 9-10',
                    'point_person' => 'James Park',
                    'status' => 'Planned'
                ]
            ],
            'budget' => [
                [
                    'activity' => 'Security Equipment',
                    'resources' => 'Security training materials, emergency kits',
                    'partners' => 'Security equipment suppliers, emergency services',
                    'amount' => '₱ 6,000'
                ],
                [
                    'activity' => 'Training Programs',
                    'resources' => 'Instructor fees, training venue, materials',
                    'partners' => 'Security training institute, local police',
                    'amount' => '₱ 4,000'
                ],
                [
                    'activity' => 'Community Patrol',
                    'resources' => 'Patrol equipment, communication devices',
                    'partners' => 'Local government, community organizations',
                    'amount' => '₱ 5,000'
                ]
            ]
        ];
    }
} elseif ($component === 'LTS') {
    if ($project_id == 1) {
        $project_data = [
            'id' => 1,
            'project_name' => 'Literacy Program for Children',
            'team_name' => 'Team Gamma',
            'team_logo' => 'team_logo.jpg',
            'component' => 'LTS',
            'nstp_section' => 'Section A',
            'submitted_date' => '2024-01-19',
            'status' => 'approved',
            'members' => [
                [
                    'name' => 'Anna Rodriguez',
                    'role' => 'Program Coordinator',
                    'email' => 'anna.rodriguez@student.edu.ph',
                    'contact' => '09123456789'
                ],
                [
                    'name' => 'Carlos Martinez',
                    'role' => 'Education Specialist',
                    'email' => 'carlos.martinez@student.edu.ph',
                    'contact' => '09123456790'
                ],
                [
                    'name' => 'Lisa Chen',
                    'role' => 'Community Outreach',
                    'email' => 'lisa.chen@student.edu.ph',
                    'contact' => '09123456791'
                ]
            ],
            'issues' => 'The community has low literacy rates among children, with limited access to educational resources and reading materials. Many children struggle with basic reading and writing skills.',
            'objectives' => 'To improve literacy rates among children in the community through interactive learning programs, reading sessions, and educational material distribution.',
            'target_community' => 'Children aged 6-12 years old in underserved communities, particularly those with limited access to educational resources.',
            'solutions' => 'Conduct weekly reading sessions, provide educational materials, organize literacy workshops, and train local volunteers to continue the program.',
            'outcomes' => 'Improved reading and writing skills, increased interest in learning, better academic performance, and sustainable literacy program in the community.',
            'activities' => [
                [
                    'stage' => 'Assessment',
                    'activities' => 'Assess current literacy levels and identify needs',
                    'timeframe' => 'Week 1-2',
                    'point_person' => 'Anna Rodriguez',
                    'status' => 'Completed'
                ],
                [
                    'stage' => 'Implementation',
                    'activities' => 'Conduct reading sessions and distribute materials',
                    'timeframe' => 'Week 3-8',
                    'point_person' => 'Carlos Martinez',
                    'status' => 'Ongoing'
                ],
                [
                    'stage' => 'Evaluation',
                    'activities' => 'Assess progress and plan sustainability',
                    'timeframe' => 'Week 9-10',
                    'point_person' => 'Lisa Chen',
                    'status' => 'Planned'
                ]
            ],
            'budget' => [
                [
                    'activity' => 'Educational Materials',
                    'resources' => 'Books, writing materials, learning aids',
                    'partners' => 'Local bookstore, educational foundation',
                    'amount' => '₱ 6,000'
                ],
                [
                    'activity' => 'Transportation',
                    'resources' => 'Vehicle rental for community visits',
                    'partners' => 'Local transport service',
                    'amount' => '₱ 2,500'
                ],
                [
                    'activity' => 'Snacks and Incentives',
                    'resources' => 'Healthy snacks and learning incentives',
                    'partners' => 'Local bakery, community kitchen',
                    'amount' => '₱ 3,500'
                ]
            ]
        ];
    } elseif ($project_id == 2) {
        $project_data = [
            'id' => 2,
            'project_name' => 'Digital Skills Training',
            'team_name' => 'Team Delta',
            'team_logo' => 'team_logo.jpg',
            'component' => 'LTS',
            'nstp_section' => 'Section A',
            'submitted_date' => '2024-01-20',
            'status' => 'ongoing',
            'members' => [
                [
                    'name' => 'Mark Johnson',
                    'role' => 'Digital Coordinator',
                    'email' => 'mark.johnson@student.edu.ph',
                    'contact' => '09123456789'
                ],
                [
                    'name' => 'Sarah Wilson',
                    'role' => 'Technology Specialist',
                    'email' => 'sarah.wilson@student.edu.ph',
                    'contact' => '09123456790'
                ],
                [
                    'name' => 'Kevin Brown',
                    'role' => 'Training Facilitator',
                    'email' => 'kevin.brown@student.edu.ph',
                    'contact' => '09123456791'
                ]
            ],
            'issues' => 'Many community members lack basic digital literacy skills, limiting their access to online resources, job opportunities, and modern communication tools. The digital divide is affecting educational and economic opportunities.',
            'objectives' => 'To bridge the digital divide by providing comprehensive digital literacy training to community members, enabling them to effectively use technology for education, work, and daily life.',
            'target_community' => 'Adults aged 18-65 years old who have limited digital skills, including seniors, unemployed individuals, and those seeking to improve their technological capabilities.',
            'solutions' => 'Conduct hands-on digital skills workshops, provide computer access, offer one-on-one training sessions, and create ongoing support programs for digital learning.',
            'outcomes' => 'Improved digital literacy rates, increased online engagement, better job prospects, enhanced communication skills, and reduced digital divide in the community.',
            'activities' => [
                [
                    'stage' => 'Setup',
                    'activities' => 'Set up computer lab and prepare training materials',
                    'timeframe' => 'Week 1-2',
                    'point_person' => 'Mark Johnson',
                    'status' => 'Completed'
                ],
                [
                    'stage' => 'Training',
                    'activities' => 'Conduct digital skills workshops and individual training',
                    'timeframe' => 'Week 3-8',
                    'point_person' => 'Sarah Wilson',
                    'status' => 'Ongoing'
                ],
                [
                    'stage' => 'Support',
                    'activities' => 'Provide ongoing support and advanced training',
                    'timeframe' => 'Week 9-10',
                    'point_person' => 'Kevin Brown',
                    'status' => 'Planned'
                ]
            ],
            'budget' => [
                [
                    'activity' => 'Computer Equipment',
                    'resources' => 'Laptops, tablets, internet connection, software',
                    'partners' => 'Technology companies, internet service providers',
                    'amount' => '₱ 15,000'
                ],
                [
                    'activity' => 'Training Materials',
                    'resources' => 'Digital learning resources, printed guides',
                    'partners' => 'Educational technology companies',
                    'amount' => '₱ 3,000'
                ],
                [
                    'activity' => 'Instructor Fees',
                    'resources' => 'Professional trainer fees and stipends',
                    'partners' => 'Local IT professionals, educational institutions',
                    'amount' => '₱ 7,000'
                ]
            ]
        ];
    } elseif ($project_id == 4) {
        $project_data = [
            'id' => 4,
            'project_name' => 'Health Education Program',
            'team_name' => 'Team Zeta',
            'team_logo' => 'team_logo.jpg',
            'component' => 'LTS',
            'nstp_section' => 'Section C',
            'submitted_date' => '2024-01-22',
            'status' => 'approved',
            'members' => [
                [
                    'name' => 'Dr. Health',
                    'role' => 'Health Coordinator',
                    'email' => 'dr.health@student.edu.ph',
                    'contact' => '09123456789'
                ],
                [
                    'name' => 'Wellness Expert',
                    'role' => 'Health Educator',
                    'email' => 'wellness.expert@student.edu.ph',
                    'contact' => '09123456790'
                ],
                [
                    'name' => 'Community Nurse',
                    'role' => 'Community Health Worker',
                    'email' => 'community.nurse@student.edu.ph',
                    'contact' => '09123456791'
                ]
            ],
            'issues' => 'The community has limited access to health education and preventive healthcare information. Many residents lack knowledge about basic health practices, disease prevention, and healthy lifestyle choices.',
            'objectives' => 'To improve community health literacy and promote healthy lifestyle choices through comprehensive health education programs, workshops, and preventive healthcare initiatives.',
            'target_community' => 'All community residents, with special focus on families, seniors, and individuals with limited access to healthcare information and services.',
            'solutions' => 'Conduct health education workshops, provide health screenings, organize wellness programs, and create accessible health information resources for the community.',
            'outcomes' => 'Improved health literacy, better preventive healthcare practices, increased awareness of healthy lifestyle choices, and enhanced community health outcomes.',
            'activities' => [
                [
                    'stage' => 'Assessment',
                    'activities' => 'Assess community health needs and knowledge gaps',
                    'timeframe' => 'Week 1-2',
                    'point_person' => 'Dr. Health',
                    'status' => 'Completed'
                ],
                [
                    'stage' => 'Education',
                    'activities' => 'Conduct health education workshops and programs',
                    'timeframe' => 'Week 3-8',
                    'point_person' => 'Wellness Expert',
                    'status' => 'Ongoing'
                ],
                [
                    'stage' => 'Screening',
                    'activities' => 'Organize health screenings and wellness checks',
                    'timeframe' => 'Week 9-10',
                    'point_person' => 'Community Nurse',
                    'status' => 'Planned'
                ]
            ],
            'budget' => [
                [
                    'activity' => 'Health Materials',
                    'resources' => 'Educational brochures, health guides, presentation materials',
                    'partners' => 'Health organizations, medical institutions',
                    'amount' => '₱ 5,000'
                ],
                [
                    'activity' => 'Screening Equipment',
                    'resources' => 'Blood pressure monitors, glucose meters, basic health tools',
                    'partners' => 'Medical equipment suppliers, health clinics',
                    'amount' => '₱ 8,000'
                ],
                [
                    'activity' => 'Workshop Venues',
                    'resources' => 'Venue rental, refreshments, workshop supplies',
                    'partners' => 'Community centers, health facilities',
                    'amount' => '₱ 4,000'
                ]
            ]
        ];
    } else { // project_id == 3
        $project_data = [
            'id' => 3,
            'project_name' => 'Environmental Awareness Campaign',
            'team_name' => 'Team Epsilon',
            'team_logo' => 'team_logo.jpg',
            'component' => 'LTS',
            'nstp_section' => 'Section B',
            'submitted_date' => '2024-01-21',
            'status' => 'ongoing',
            'members' => [
                [
                    'name' => 'Green Earth',
                    'role' => 'Environmental Coordinator',
                    'email' => 'green.earth@student.edu.ph',
                    'contact' => '09123456789'
                ],
                [
                    'name' => 'Eco Warrior',
                    'role' => 'Campaign Manager',
                    'email' => 'eco.warrior@student.edu.ph',
                    'contact' => '09123456790'
                ],
                [
                    'name' => 'Nature Lover',
                    'role' => 'Community Educator',
                    'email' => 'nature.lover@student.edu.ph',
                    'contact' => '09123456791'
                ]
            ],
            'issues' => 'The community lacks awareness about environmental issues and sustainable practices. Many residents are not informed about proper waste management, conservation, and environmental protection measures.',
            'objectives' => 'To raise environmental awareness and promote sustainable practices in the community through educational campaigns, workshops, and hands-on environmental activities.',
            'target_community' => 'All community residents, with special focus on families, students, and local businesses to promote environmental responsibility.',
            'solutions' => 'Conduct environmental education workshops, organize community clean-up drives, promote recycling programs, and create environmental awareness materials.',
            'outcomes' => 'Increased environmental awareness, improved waste management practices, community engagement in conservation, and sustainable environmental practices adoption.',
            'activities' => [
                [
                    'stage' => 'Research',
                    'activities' => 'Research environmental issues and prepare educational materials',
                    'timeframe' => 'Week 1-2',
                    'point_person' => 'Green Earth',
                    'status' => 'Completed'
                ],
                [
                    'stage' => 'Campaign',
                    'activities' => 'Conduct awareness campaigns and workshops',
                    'timeframe' => 'Week 3-8',
                    'point_person' => 'Eco Warrior',
                    'status' => 'Ongoing'
                ],
                [
                    'stage' => 'Action',
                    'activities' => 'Organize community clean-up and conservation activities',
                    'timeframe' => 'Week 9-10',
                    'point_person' => 'Nature Lover',
                    'status' => 'Planned'
                ]
            ],
            'budget' => [
                [
                    'activity' => 'Educational Materials',
                    'resources' => 'Brochures, posters, educational videos, presentation materials',
                    'partners' => 'Printing companies, environmental organizations',
                    'amount' => '₱ 4,000'
                ],
                [
                    'activity' => 'Clean-up Equipment',
                    'resources' => 'Cleaning supplies, gloves, trash bags, tools',
                    'partners' => 'Hardware stores, environmental supply companies',
                    'amount' => '₱ 3,000'
                ],
                [
                    'activity' => 'Workshop Materials',
                    'resources' => 'Workshop supplies, refreshments, venue rental',
                    'partners' => 'Community centers, local businesses',
                    'amount' => '₱ 3,000'
                ]
            ]
        ];
    }
} else { // CWTS
    if ($project_id == 1) {
        $project_data = [
            'id' => 1,
            'project_name' => 'Cuento Diatun',
            'team_name' => 'Team Aro',
            'team_logo' => 'team_logo.jpg',
            'component' => 'CWTS',
            'nstp_section' => 'Section A',
            'submitted_date' => '2024-01-17',
            'status' => 'approved',
            'members' => [
                [
                    'name' => 'Juan Dela Cruz',
                    'role' => 'Team Leader',
                    'email' => 'juan.delacruz@student.edu.ph',
                    'contact' => '09123456789'
                ],
                [
                    'name' => 'Maria Santos',
                    'role' => 'Project Coordinator',
                    'email' => 'maria.santos@student.edu.ph',
                    'contact' => '09123456790'
                ],
                [
                    'name' => 'Jose Garcia',
                    'role' => 'Documentation Officer',
                    'email' => 'jose.garcia@student.edu.ph',
                    'contact' => '09123456791'
                ]
            ],
            'issues' => 'The community lacks access to quality educational materials and storytelling resources for children. Many children in the area have limited exposure to reading materials and interactive learning experiences.',
            'objectives' => 'To provide interactive storytelling sessions and educational materials to children in the community, promoting literacy and creativity through engaging activities.',
            'target_community' => 'Children aged 5-12 years old in Barangay San Jose, particularly those from low-income families with limited access to educational resources.',
            'solutions' => 'Conduct weekly storytelling sessions, distribute educational materials, organize reading corners in community centers, and train local volunteers to continue the program.',
            'outcomes' => 'Improved literacy rates among children, increased community engagement in education, sustainable reading program, and enhanced learning environment in the community.',
            'activities' => [
                [
                    'stage' => 'Planning',
                    'activities' => 'Project planning and resource preparation',
                    'timeframe' => 'Week 1-2',
                    'point_person' => 'Juan Dela Cruz',
                    'status' => 'Completed'
                ],
                [
                    'stage' => 'Implementation',
                    'activities' => 'Conduct storytelling sessions and distribute materials',
                    'timeframe' => 'Week 3-8',
                    'point_person' => 'Maria Santos',
                    'status' => 'Ongoing'
                ],
                [
                    'stage' => 'Evaluation',
                    'activities' => 'Assess program impact and gather feedback',
                    'timeframe' => 'Week 9-10',
                    'point_person' => 'Jose Garcia',
                    'status' => 'Planned'
                ]
            ],
            'budget' => [
                [
                    'activity' => 'Educational Materials',
                    'resources' => 'Books, art supplies, learning aids',
                    'partners' => 'Local bookstore, educational foundation',
                    'amount' => '₱ 5,000'
                ],
                [
                    'activity' => 'Transportation',
                    'resources' => 'Vehicle rental, fuel',
                    'partners' => 'Local transport service',
                    'amount' => '₱ 2,000'
                ],
                [
                    'activity' => 'Snacks and Refreshments',
                    'resources' => 'Healthy snacks for children',
                    'partners' => 'Local bakery, community kitchen',
                    'amount' => '₱ 3,000'
                ]
            ]
        ];
    } elseif ($project_id == 2) {
        $project_data = [
            'id' => 2,
            'project_name' => 'Community Garden Project',
            'team_name' => 'Team Beta',
            'team_logo' => 'team_logo.jpg',
            'component' => 'CWTS',
            'nstp_section' => 'Section A',
            'submitted_date' => '2024-01-18',
            'status' => 'ongoing',
            'members' => [
                [
                    'name' => 'Garden Master',
                    'role' => 'Garden Coordinator',
                    'email' => 'garden.master@student.edu.ph',
                    'contact' => '09123456789'
                ],
                [
                    'name' => 'Plant Expert',
                    'role' => 'Agricultural Specialist',
                    'email' => 'plant.expert@student.edu.ph',
                    'contact' => '09123456790'
                ],
                [
                    'name' => 'Community Builder',
                    'role' => 'Community Organizer',
                    'email' => 'community.builder@student.edu.ph',
                    'contact' => '09123456791'
                ]
            ],
            'issues' => 'The community lacks access to fresh, healthy food and green spaces. Many residents have limited knowledge about sustainable gardening and food production, leading to poor nutrition and lack of community bonding.',
            'objectives' => 'To establish a community garden that provides fresh produce, promotes sustainable living, and creates a space for community members to learn about gardening and healthy eating.',
            'target_community' => 'All community residents, with special focus on families, seniors, and children who can benefit from fresh produce and gardening education.',
            'solutions' => 'Establish community garden plots, provide gardening training, organize community work days, and create educational programs about sustainable agriculture.',
            'outcomes' => 'Increased access to fresh produce, improved community bonding, enhanced knowledge of sustainable gardening, and creation of a sustainable community resource.',
            'activities' => [
                [
                    'stage' => 'Planning',
                    'activities' => 'Site selection, garden design, and resource planning',
                    'timeframe' => 'Week 1-2',
                    'point_person' => 'Garden Master',
                    'status' => 'Completed'
                ],
                [
                    'stage' => 'Setup',
                    'activities' => 'Prepare garden beds, install irrigation, plant seeds',
                    'timeframe' => 'Week 3-8',
                    'point_person' => 'Plant Expert',
                    'status' => 'Ongoing'
                ],
                [
                    'stage' => 'Maintenance',
                    'activities' => 'Ongoing garden maintenance and community education',
                    'timeframe' => 'Week 9-10',
                    'point_person' => 'Community Builder',
                    'status' => 'Planned'
                ]
            ],
            'budget' => [
                [
                    'activity' => 'Garden Supplies',
                    'resources' => 'Seeds, soil, tools, irrigation equipment',
                    'partners' => 'Local nurseries, agricultural supply stores',
                    'amount' => '₱ 8,000'
                ],
                [
                    'activity' => 'Infrastructure',
                    'resources' => 'Garden beds, fencing, water system',
                    'partners' => 'Local contractors, hardware stores',
                    'amount' => '₱ 6,000'
                ],
                [
                    'activity' => 'Educational Materials',
                    'resources' => 'Gardening guides, workshop materials',
                    'partners' => 'Agricultural extension office, educational institutions',
                    'amount' => '₱ 2,000'
                ]
            ]
        ];
    } elseif ($project_id == 4) {
        $project_data = [
            'id' => 4,
            'project_name' => 'Health Awareness Campaign',
            'team_name' => 'Team Delta',
            'team_logo' => 'team_logo.jpg',
            'component' => 'CWTS',
            'nstp_section' => 'Section C',
            'submitted_date' => '2024-01-20',
            'status' => 'approved',
            'members' => [
                [
                    'name' => 'Health Advocate',
                    'role' => 'Campaign Coordinator',
                    'email' => 'health.advocate@student.edu.ph',
                    'contact' => '09123456789'
                ],
                [
                    'name' => 'Wellness Champion',
                    'role' => 'Health Promoter',
                    'email' => 'wellness.champion@student.edu.ph',
                    'contact' => '09123456790'
                ],
                [
                    'name' => 'Community Health',
                    'role' => 'Community Outreach Specialist',
                    'email' => 'community.health@student.edu.ph',
                    'contact' => '09123456791'
                ]
            ],
            'issues' => 'The community lacks awareness about important health issues and preventive measures. Many residents are not informed about common diseases, health risks, and the importance of regular health check-ups.',
            'objectives' => 'To raise health awareness in the community through educational campaigns, health screenings, and preventive healthcare initiatives to improve overall community health and well-being.',
            'target_community' => 'All community residents, with special focus on vulnerable populations including seniors, children, and families with limited access to healthcare information.',
            'solutions' => 'Conduct health awareness campaigns, organize health screenings, provide health education materials, and establish partnerships with local health facilities.',
            'outcomes' => 'Increased health awareness, improved preventive healthcare practices, better health-seeking behavior, and enhanced community health outcomes.',
            'activities' => [
                [
                    'stage' => 'Planning',
                    'activities' => 'Plan campaign activities and prepare health materials',
                    'timeframe' => 'Week 1-2',
                    'point_person' => 'Health Advocate',
                    'status' => 'Completed'
                ],
                [
                    'stage' => 'Campaign',
                    'activities' => 'Conduct health awareness campaigns and workshops',
                    'timeframe' => 'Week 3-8',
                    'point_person' => 'Wellness Champion',
                    'status' => 'Ongoing'
                ],
                [
                    'stage' => 'Screening',
                    'activities' => 'Organize health screenings and follow-up activities',
                    'timeframe' => 'Week 9-10',
                    'point_person' => 'Community Health',
                    'status' => 'Planned'
                ]
            ],
            'budget' => [
                [
                    'activity' => 'Campaign Materials',
                    'resources' => 'Health posters, brochures, educational videos, banners',
                    'partners' => 'Printing companies, health organizations',
                    'amount' => '₱ 6,000'
                ],
                [
                    'activity' => 'Health Screenings',
                    'resources' => 'Screening equipment, medical supplies, health professionals',
                    'partners' => 'Local health clinics, medical professionals',
                    'amount' => '₱ 10,000'
                ],
                [
                    'activity' => 'Venue and Logistics',
                    'resources' => 'Venue rental, transportation, refreshments',
                    'partners' => 'Community centers, local businesses',
                    'amount' => '₱ 4,000'
                ]
            ]
        ];
    } else { // project_id == 3
        $project_data = [
            'id' => 3,
            'project_name' => 'Environmental Cleanup Drive',
            'team_name' => 'Team Charlie',
            'team_logo' => 'team_logo.jpg',
            'component' => 'CWTS',
            'nstp_section' => 'Section B',
            'submitted_date' => '2024-01-19',
            'status' => 'approved',
            'members' => [
                [
                    'name' => 'Clean Up',
                    'role' => 'Cleanup Coordinator',
                    'email' => 'clean.up@student.edu.ph',
                    'contact' => '09123456789'
                ],
                [
                    'name' => 'Waste Manager',
                    'role' => 'Waste Management Specialist',
                    'email' => 'waste.manager@student.edu.ph',
                    'contact' => '09123456790'
                ],
                [
                    'name' => 'Community Helper',
                    'role' => 'Volunteer Coordinator',
                    'email' => 'community.helper@student.edu.ph',
                    'contact' => '09123456791'
                ]
            ],
            'issues' => 'The community suffers from poor waste management and environmental pollution. Litter and improper waste disposal are affecting the health and aesthetics of the neighborhood.',
            'objectives' => 'To organize community-wide cleanup activities and establish proper waste management practices to improve environmental health and community cleanliness.',
            'target_community' => 'All community residents, local businesses, and organizations that can participate in cleanup activities and adopt better waste management practices.',
            'solutions' => 'Organize regular cleanup drives, establish waste segregation programs, educate residents on proper waste disposal, and create sustainable waste management systems.',
            'outcomes' => 'Cleaner community environment, improved waste management practices, increased environmental awareness, and sustainable cleanup programs.',
            'activities' => [
                [
                    'stage' => 'Assessment',
                    'activities' => 'Assess waste problems and identify cleanup areas',
                    'timeframe' => 'Week 1-2',
                    'point_person' => 'Clean Up',
                    'status' => 'Completed'
                ],
                [
                    'stage' => 'Cleanup',
                    'activities' => 'Organize and conduct community cleanup drives',
                    'timeframe' => 'Week 3-8',
                    'point_person' => 'Waste Manager',
                    'status' => 'Ongoing'
                ],
                [
                    'stage' => 'Education',
                    'activities' => 'Educate community on proper waste management',
                    'timeframe' => 'Week 9-10',
                    'point_person' => 'Community Helper',
                    'status' => 'Planned'
                ]
            ],
            'budget' => [
                [
                    'activity' => 'Cleanup Supplies',
                    'resources' => 'Gloves, trash bags, cleaning tools, safety equipment',
                    'partners' => 'Hardware stores, safety equipment suppliers',
                    'amount' => '₱ 4,000'
                ],
                [
                    'activity' => 'Waste Disposal',
                    'resources' => 'Waste collection and proper disposal fees',
                    'partners' => 'Local waste management company',
                    'amount' => '₱ 3,000'
                ],
                [
                    'activity' => 'Educational Materials',
                    'resources' => 'Educational posters, brochures, workshop materials',
                    'partners' => 'Printing companies, environmental organizations',
                    'amount' => '₱ 2,000'
                ]
            ]
        ];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Project Details - <?php echo htmlspecialchars($project_data['project_name']); ?></title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    .light-pink-bg { 
      background: linear-gradient(135deg, #FFE4E1 0%, #FFF0F5 100%);
    }
    .pill-bg { 
      background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
      border: 1px solid #e2e8f0;
    }
    .card-shadow {
      box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    }
    .card-hover {
      transition: all 0.3s ease;
    }
    .card-hover:hover {
      transform: translateY(-2px);
      box-shadow: 0 10px 25px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }
    .gradient-text {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }
    .status-badge {
      display: inline-flex;
      align-items: center;
      gap: 0.25rem;
    }
    .status-badge::before {
      content: '';
      width: 6px;
      height: 6px;
      border-radius: 50%;
      background-color: currentColor;
    }
  </style>
</head>
<body class="bg-gray-100 flex">

  <!-- Sidebar -->
  <?php include '../components/sidebar.php'; ?>

  <!-- Main Content -->
  <main id="content" class="flex-1 md:ml-64 p-4 md:p-6 transition-all duration-300 bg-white min-h-screen">

    <!-- Page Header -->
    <div class="mb-6 md:mb-8">
      <!-- Back Button -->
      <div class="mb-4">
        <?php $backLink = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'current_projects.php'; ?>
        <?php include '../components/back_button.php'; ?>
      </div>
      
      <!-- Project Title with Logo -->
      <div class="bg-gradient-to-r from-gray-50 to-white p-4 md:p-6 rounded-2xl shadow-sm border border-gray-100">
        <div class="flex items-center gap-4">
          <!-- Team Logo -->
          <div class="flex-shrink-0">
            <?php 
              $component = $project_data['component'];
              $gradientClass = '';
              $componentIcon = '';
              
              if ($component === 'ROTC') {
                $gradientClass = 'from-red-500 to-orange-600';
                $componentIcon = '🎖️';
              } elseif ($component === 'LTS') {
                $gradientClass = 'from-green-500 to-emerald-600';
                $componentIcon = '📚';
              } else { // CWTS
                $gradientClass = 'from-blue-500 to-purple-600';
                $componentIcon = '🤝';
              }
            ?>
            <div class="w-16 h-16 md:w-20 md:h-20 bg-gradient-to-br <?php echo $gradientClass; ?> rounded-2xl flex flex-col items-center justify-center shadow-lg border-4 border-white">
              <span class="text-lg md:text-xl mb-1"><?php echo $componentIcon; ?></span>
              <span class="text-xs md:text-sm font-bold text-white">
                <?php echo strtoupper(substr($project_data['team_name'], 5, 1)); ?>
              </span>
            </div>
          </div>
          
          <!-- Project Info -->
          <div class="flex-1 min-w-0">
            <h1 class="text-xl md:text-4xl font-bold text-black mb-1 md:mb-2"><?php echo htmlspecialchars($project_data['project_name']); ?></h1>
            <p class="text-sm md:text-lg text-gray-700 mb-1">by <?php echo htmlspecialchars($project_data['team_name']); ?></p>
            <div class="flex flex-wrap items-center gap-2 mt-2">
              <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                <?php echo htmlspecialchars($project_data['component']); ?>
              </span>
              <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                <?php echo htmlspecialchars($project_data['nstp_section']); ?>
              </span>
              <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                Approved
              </span>
              <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                Ongoing
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Project Details Container -->
    <div class="light-pink-bg p-4 md:p-6 rounded-2xl space-y-6 md:space-y-8 card-shadow">

      <!-- TEAM INFORMATION -->
      <div class="pill-bg p-4 md:p-6 rounded-xl card-shadow card-hover">
        <div class="flex items-center gap-3 mb-6">
          <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-purple-600 rounded-xl flex items-center justify-center text-white text-xl font-bold shadow-lg">
            👥
          </div>
          <div>
            <h2 class="text-xl md:text-2xl font-bold gradient-text">Team Information</h2>
            <p class="text-sm text-gray-600">Project team details and composition</p>
          </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
          <div>
            <label class="block text-base md:text-lg font-medium text-gray-700">Project Name</label>
            <p class="text-base md:text-lg text-gray-900 mt-1"><?php echo htmlspecialchars($project_data['project_name']); ?></p>
          </div>
          <div>
            <label class="block text-base md:text-lg font-medium text-gray-700">Team Name</label>
            <p class="text-base md:text-lg text-gray-900 mt-1"><?php echo htmlspecialchars($project_data['team_name']); ?></p>
          </div>
          <div>
            <label class="block text-base md:text-lg font-medium text-gray-700">Component</label>
            <p class="text-base md:text-lg text-gray-900 mt-1"><?php echo htmlspecialchars($project_data['component']); ?></p>
          </div>
          <div>
            <label class="block text-base md:text-lg font-medium text-gray-700">Section</label>
            <p class="text-base md:text-lg text-gray-900 mt-1"><?php echo htmlspecialchars($project_data['nstp_section']); ?></p>
          </div>
          <div>
            <label class="block text-base md:text-lg font-medium text-gray-700">Submitted Date</label>
            <p class="text-base md:text-lg text-gray-900 mt-1"><?php echo htmlspecialchars($project_data['submitted_date']); ?></p>
          </div>
          <div>
            <label class="block text-base md:text-lg font-medium text-gray-700">Status</label>
            <div class="flex flex-wrap gap-2 mt-1">
              <span class="inline-block px-3 py-1 rounded-full text-xs md:text-sm font-medium bg-green-100 text-green-800">
                Approved
              </span>
              <span class="inline-block px-3 py-1 rounded-full text-xs md:text-sm font-medium bg-blue-100 text-blue-800">
                Ongoing
              </span>
            </div>
          </div>
        </div>
      </div>

      <!-- MEMBER PROFILE -->
      <div class="pill-bg p-4 md:p-6 rounded-xl card-shadow card-hover">
        <div class="flex items-center gap-3 mb-6">
          <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-teal-600 rounded-xl flex items-center justify-center text-white text-xl font-bold shadow-lg">
            👤
          </div>
          <div>
            <h2 class="text-xl md:text-2xl font-bold gradient-text">Member Profile</h2>
            <p class="text-sm text-gray-600">Team members and their roles</p>
          </div>
        </div>

        <!-- Desktop Table View -->
        <div class="hidden md:block overflow-x-auto">
          <table class="w-full text-left bg-white rounded-lg shadow-sm">
            <thead class="bg-gray-100">
              <tr>
                <th class="px-4 py-3 text-base font-semibold">Name</th>
                <th class="px-4 py-3 text-base font-semibold">Role/s</th>
                <th class="px-4 py-3 text-base font-semibold">School Email</th>
                <th class="px-4 py-3 text-base font-semibold">Contact Number</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($project_data['members'] as $member): ?>
                <tr class="border-t">
                  <td class="px-4 py-3 text-base"><?php echo htmlspecialchars($member['name']); ?></td>
                  <td class="px-4 py-3 text-base"><?php echo htmlspecialchars($member['role']); ?></td>
                  <td class="px-4 py-3 text-base"><?php echo htmlspecialchars($member['email']); ?></td>
                  <td class="px-4 py-3 text-base"><?php echo htmlspecialchars($member['contact']); ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>

        <!-- Mobile Card View -->
        <div class="md:hidden space-y-4">
          <?php foreach ($project_data['members'] as $index => $member): ?>
            <div class="bg-white p-4 rounded-xl shadow-md border border-gray-100 card-hover">
              <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-full flex items-center justify-center text-white font-bold text-sm">
                  <?php echo strtoupper(substr($member['name'], 0, 2)); ?>
                </div>
                <div>
                  <h3 class="font-semibold text-gray-900"><?php echo htmlspecialchars($member['name']); ?></h3>
                  <p class="text-xs text-gray-500">Team Member #<?php echo $index + 1; ?></p>
                </div>
              </div>
              <div class="space-y-3">
                <div class="flex items-center gap-2">
                  <span class="text-blue-500">🎯</span>
                  <div>
                    <label class="block text-xs font-medium text-gray-600">Role</label>
                    <p class="text-sm font-medium"><?php echo htmlspecialchars($member['role']); ?></p>
                  </div>
                </div>
                <div class="flex items-center gap-2">
                  <span class="text-green-500">📧</span>
                  <div>
                    <label class="block text-xs font-medium text-gray-600">Email</label>
                    <p class="text-sm"><?php echo htmlspecialchars($member['email']); ?></p>
                  </div>
                </div>
                <div class="flex items-center gap-2">
                  <span class="text-purple-500">📱</span>
                  <div>
                    <label class="block text-xs font-medium text-gray-600">Contact</label>
                    <p class="text-sm"><?php echo htmlspecialchars($member['contact']); ?></p>
                  </div>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- PROJECT DETAILS -->
      <div class="pill-bg p-4 md:p-6 rounded-xl card-shadow card-hover">
        <div class="flex items-center gap-3 mb-6">
          <div class="w-12 h-12 bg-gradient-to-br from-orange-500 to-red-600 rounded-xl flex items-center justify-center text-white text-xl font-bold shadow-lg">
            🎯
          </div>
          <div>
            <h2 class="text-xl md:text-2xl font-bold gradient-text">Project Details</h2>
            <p class="text-sm text-gray-600">Comprehensive project information and objectives</p>
          </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
          <div class="bg-white p-4 rounded-xl border border-gray-100 card-hover">
            <div class="flex items-center gap-2 mb-3">
              <span class="text-red-500">⚠️</span>
              <label class="text-base md:text-lg font-semibold text-gray-800">Issues/Problems</label>
            </div>
            <p class="text-gray-700 text-sm md:text-base leading-relaxed"><?php echo nl2br(htmlspecialchars($project_data['issues'])); ?></p>
          </div>
          
          <div class="bg-white p-4 rounded-xl border border-gray-100 card-hover">
            <div class="flex items-center gap-2 mb-3">
              <span class="text-green-500">🎯</span>
              <label class="text-base md:text-lg font-semibold text-gray-800">Goals/Objectives</label>
            </div>
            <p class="text-gray-700 text-sm md:text-base leading-relaxed"><?php echo nl2br(htmlspecialchars($project_data['objectives'])); ?></p>
          </div>
          
          <div class="bg-white p-4 rounded-xl border border-gray-100 card-hover">
            <div class="flex items-center gap-2 mb-3">
              <span class="text-blue-500">👥</span>
              <label class="text-base md:text-lg font-semibold text-gray-800">Target Community</label>
            </div>
            <p class="text-gray-700 text-sm md:text-base leading-relaxed"><?php echo nl2br(htmlspecialchars($project_data['target_community'])); ?></p>
          </div>
          
          <div class="bg-white p-4 rounded-xl border border-gray-100 card-hover">
            <div class="flex items-center gap-2 mb-3">
              <span class="text-purple-500">⚡</span>
              <label class="text-base md:text-lg font-semibold text-gray-800">Solutions/Activities</label>
            </div>
            <p class="text-gray-700 text-sm md:text-base leading-relaxed"><?php echo nl2br(htmlspecialchars($project_data['solutions'])); ?></p>
          </div>
          
          <div class="lg:col-span-2 bg-white p-4 rounded-xl border border-gray-100 card-hover">
            <div class="flex items-center gap-2 mb-3">
              <span class="text-yellow-500">🌟</span>
              <label class="text-base md:text-lg font-semibold text-gray-800">Expected Outcomes</label>
            </div>
            <p class="text-gray-700 text-sm md:text-base leading-relaxed"><?php echo nl2br(htmlspecialchars($project_data['outcomes'])); ?></p>
          </div>
        </div>
      </div>

      <!-- PROJECT ACTIVITIES -->
      <div class="pill-bg p-4 md:p-6 rounded-xl card-shadow card-hover">
        <div class="flex items-center gap-3 mb-6">
          <div class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-blue-600 rounded-xl flex items-center justify-center text-white text-xl font-bold shadow-lg">
            📅
          </div>
          <div>
            <h2 class="text-xl md:text-2xl font-bold gradient-text">Project Activities</h2>
            <p class="text-sm text-gray-600">Timeline and progress tracking</p>
          </div>
        </div>

        <!-- Desktop Table View -->
        <div class="hidden lg:block overflow-x-auto">
          <table class="w-full text-left bg-white rounded-xl shadow-sm border border-gray-100">
            <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
              <tr>
                <th class="px-4 py-3 text-base font-semibold text-gray-700">Stage</th>
                <th class="px-4 py-3 text-base font-semibold text-gray-700">Activities</th>
                <th class="px-4 py-3 text-base font-semibold text-gray-700">Timeline</th>
                <th class="px-4 py-3 text-base font-semibold text-gray-700">Point Person</th>
                <th class="px-4 py-3 text-base font-semibold text-gray-700">Status</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($project_data['activities'] as $activity): ?>
                <tr class="border-t border-gray-100 hover:bg-gray-50 transition-colors">
                  <td class="px-4 py-3 text-base font-medium"><?php echo htmlspecialchars($activity['stage']); ?></td>
                  <td class="px-4 py-3 text-base"><?php echo htmlspecialchars($activity['activities']); ?></td>
                  <td class="px-4 py-3 text-base"><?php echo htmlspecialchars($activity['timeframe']); ?></td>
                  <td class="px-4 py-3 text-base"><?php echo htmlspecialchars($activity['point_person']); ?></td>
                  <td class="px-4 py-3 text-base">
                    <span class="status-badge px-3 py-1 rounded-full text-sm font-medium
                      <?php echo $activity['status'] === 'Completed' ? 'bg-green-100 text-green-800' : 
                                ($activity['status'] === 'Ongoing' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800'); ?>">
                      <?php echo htmlspecialchars($activity['status']); ?>
                    </span>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>

        <!-- Mobile Card View -->
        <div class="lg:hidden space-y-4">
          <?php foreach ($project_data['activities'] as $index => $activity): ?>
            <div class="bg-white p-4 rounded-xl shadow-md border border-gray-100 card-hover">
              <div class="flex items-center justify-between mb-3">
                <div class="flex items-center gap-2">
                  <div class="w-8 h-8 bg-gradient-to-br from-indigo-500 to-blue-600 rounded-lg flex items-center justify-center text-white font-bold text-xs">
                    <?php echo $index + 1; ?>
                  </div>
                  <h3 class="font-semibold text-gray-900"><?php echo htmlspecialchars($activity['stage']); ?></h3>
                </div>
                <span class="status-badge px-2 py-1 rounded-full text-xs font-medium
                  <?php echo $activity['status'] === 'Completed' ? 'bg-green-100 text-green-800' : 
                            ($activity['status'] === 'Ongoing' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800'); ?>">
                  <?php echo htmlspecialchars($activity['status']); ?>
                </span>
              </div>
              <div class="space-y-3">
                <div>
                  <label class="block text-xs font-medium text-gray-600 mb-1">Activities</label>
                  <p class="text-sm text-gray-800"><?php echo htmlspecialchars($activity['activities']); ?></p>
                </div>
                <div class="grid grid-cols-2 gap-3">
                  <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Timeline</label>
                    <p class="text-sm text-gray-800"><?php echo htmlspecialchars($activity['timeframe']); ?></p>
                  </div>
                  <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Point Person</label>
                    <p class="text-sm text-gray-800"><?php echo htmlspecialchars($activity['point_person']); ?></p>
                  </div>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- BUDGET -->
      <div class="pill-bg p-4 md:p-6 rounded-xl card-shadow card-hover">
        <div class="flex items-center gap-3 mb-6">
          <div class="w-12 h-12 bg-gradient-to-br from-emerald-500 to-green-600 rounded-xl flex items-center justify-center text-white text-xl font-bold shadow-lg">
            💰
          </div>
          <div>
            <h2 class="text-xl md:text-2xl font-bold gradient-text">Budget Breakdown</h2>
            <p class="text-sm text-gray-600">Financial planning and resource allocation</p>
          </div>
        </div>

        <!-- Desktop Table View -->
        <div class="hidden lg:block overflow-x-auto">
          <table class="w-full text-left bg-white rounded-xl shadow-sm border border-gray-100">
            <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
              <tr>
                <th class="px-4 py-3 text-base font-semibold text-gray-700">Activity</th>
                <th class="px-4 py-3 text-base font-semibold text-gray-700">Resources</th>
                <th class="px-4 py-3 text-base font-semibold text-gray-700">Partners</th>
                <th class="px-4 py-3 text-base font-semibold text-gray-700">Amount</th>
              </tr>
            </thead>
            <tbody>
              <?php 
                $total_desktop = 0;
                foreach ($project_data['budget'] as $budget_item): 
                  $amount = (int) filter_var($budget_item['amount'], FILTER_SANITIZE_NUMBER_INT);
                  $total_desktop += $amount;
              ?>
                <tr class="border-t border-gray-100 hover:bg-gray-50 transition-colors">
                  <td class="px-4 py-3 text-base font-medium"><?php echo htmlspecialchars($budget_item['activity']); ?></td>
                  <td class="px-4 py-3 text-base"><?php echo htmlspecialchars($budget_item['resources']); ?></td>
                  <td class="px-4 py-3 text-base"><?php echo htmlspecialchars($budget_item['partners']); ?></td>
                  <td class="px-4 py-3 text-base font-bold text-green-600"><?php echo htmlspecialchars($budget_item['amount']); ?></td>
                </tr>
              <?php endforeach; ?>
              <!-- Total Row for Desktop -->
              <tr class="border-t-2 border-green-200 bg-gradient-to-r from-green-50 to-emerald-50">
                <td class="px-4 py-4 text-base font-bold text-gray-800" colspan="3">Total Project Budget</td>
                <td class="px-4 py-4 text-xl font-bold text-green-600">₱ <?php echo number_format($total_desktop); ?></td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Mobile Card View -->
        <div class="lg:hidden space-y-4">
          <?php 
            $total = 0;
            foreach ($project_data['budget'] as $budget_item): 
              $amount = (int) filter_var($budget_item['amount'], FILTER_SANITIZE_NUMBER_INT);
              $total += $amount;
          ?>
            <div class="bg-white p-4 rounded-xl shadow-md border border-gray-100 card-hover">
              <div class="flex items-center justify-between mb-3 gap-3">
                <h3 class="font-semibold text-gray-900 flex-1 min-w-0"><?php echo htmlspecialchars($budget_item['activity']); ?></h3>
                <span class="text-lg font-bold text-green-600 whitespace-nowrap flex-shrink-0"><?php echo htmlspecialchars($budget_item['amount']); ?></span>
              </div>
              <div class="space-y-2">
                <div>
                  <label class="block text-xs font-medium text-gray-600 mb-1">Resources Needed</label>
                  <p class="text-sm text-gray-800"><?php echo htmlspecialchars($budget_item['resources']); ?></p>
                </div>
                <div>
                  <label class="block text-xs font-medium text-gray-600 mb-1">Partner Agencies</label>
                  <p class="text-sm text-gray-800"><?php echo htmlspecialchars($budget_item['partners']); ?></p>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
          
          <!-- Total Budget Card -->
          <div class="bg-gradient-to-r from-green-500 to-emerald-600 p-4 rounded-xl text-white shadow-lg">
            <div class="flex items-center justify-between">
              <div>
                <h3 class="text-lg font-bold">Total Project Budget</h3>
                <p class="text-sm opacity-90">Complete financial allocation</p>
              </div>
              <div class="text-right">
                <p class="text-2xl font-bold whitespace-nowrap">₱ <?php echo number_format($total); ?></p>
              </div>
            </div>
          </div>
        </div>
      </div>

    </div>
  </main>

  <!-- JS -->
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const menuBtn = document.getElementById('menuBtn');
      const sidebar = document.getElementById('sidebar');

      if (menuBtn && sidebar) {
        menuBtn.addEventListener('click', () => {
          sidebar.classList.toggle('-translate-x-full');
        });
      }
    });
  </script>

</body>
</html>
