<?php

namespace App\controllers\client;

use App\Models\Blog;

class BlogController
{
    public function index()
    {
        // Lấy trang hiện tại từ URL
        $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
        $perPage = 9; // Số bài viết mỗi trang

        // Lấy danh sách blog đã published với phân trang
        $blogData = Blog::paginatePublished($page, $perPage);

        // dd($blogData);

        $data = [
            'title' => 'Blog / Tin tức - PawSpa',
            'meta_description' => 'Khám phá các bài viết hữu ích về chăm sóc thú cưng, mẹo nuôi dưỡng và dịch vụ spa cho thú cưng tại PawSpa.',
            'blogs' => $blogData['data'],
            'pagination' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total' => $blogData['total'],
                'total_pages' => $blogData['total_pages'],
                'has_prev' => $page > 1,
                'has_next' => $page < $blogData['total_pages'],
                'prev_page' => $page - 1,
                'next_page' => $page + 1
            ]
        ];

        render_view('client/blog/news', $data, 'client');
    }

    public function detail($slug)
    {
        // Tìm blog theo slug
        $blog = Blog::findBySlug($slug);

        if (!$blog || $blog->status !== 'published') {
            // Redirect về trang 404 hoặc blog index
            header('HTTP/1.0 404 Not Found');
            $data = [
                'title' => 'Không tìm thấy bài viết - PawSpa',
                'message' => 'Bài viết bạn tìm kiếm không tồn tại hoặc đã bị xóa.'
            ];
            render_view('client/errors/404', $data, 'client');
            return;
        }

        // Tăng view count
        Blog::incrementViewCount($blog->blog_id);

        // Lấy các bài viết liên quan (cùng status published, trừ bài hiện tại)
        $relatedBlogs = Blog::getRelatedBlogs($blog->blog_id, 4);

        $data = [
            'title' => $blog->meta_title ?: $blog->title,
            'meta_description' => $blog->meta_description ?: $blog->getExcerpt(160),
            'meta_keywords' => 'thú cưng, chăm sóc thú cưng, PawSpa, ' . strtolower($blog->title),
            'og_title' => $blog->title,
            'og_description' => $blog->getExcerpt(160),
            'og_image' => $blog->featured_image ? base_url($blog->featured_image) : base_url('assets/images/default-blog.png'),
            'canonical_url' => base_url('blog/' . $blog->slug),
            'blog' => $blog,
            'related_blogs' => $relatedBlogs
        ];

        render_view('client/blog/detail', $data, 'client');
    }

    public function search()
    {
        $keyword = isset($_GET['q']) ? trim($_GET['q']) : '';
        $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
        $perPage = 9;

        if (empty($keyword)) {
            // Redirect về trang blog chính nếu không có từ khóa
            header('Location: ' . base_url('blog'));
            exit;
        }

        // Tìm kiếm blog
        $blogData = Blog::searchPublished($keyword, $page, $perPage);

        $data = [
            'title' => "Tìm kiếm: {$keyword} - PawSpa Blog",
            'meta_description' => "Kết quả tìm kiếm cho từ khóa '{$keyword}' trong blog PawSpa về chăm sóc thú cưng.",
            'keyword' => $keyword,
            'blogs' => $blogData['data'],
            'pagination' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total' => $blogData['total'],
                'total_pages' => $blogData['total_pages'],
                'has_prev' => $page > 1,
                'has_next' => $page < $blogData['total_pages'],
                'prev_page' => $page - 1,
                'next_page' => $page + 1
            ]
        ];

        render_view('client/blog/search', $data, 'client');
    }
}
