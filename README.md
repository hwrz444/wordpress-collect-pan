# 采集 + 网盘转存（WordPress 插件）

一个把 **网页采集** 和 **网盘自动转存分享** 合在一起的 WordPress 插件：

- 采集任意网站的文章（XPath 规则、内容 2/3/4/5 拼接、定时任务、日志）
- 把文章里的网盘链接自动变成「转存按钮」
- 支持 **夸克 / UC / 百度 / 迅雷 / 阿里云盘** 五种网盘：自动转存到你自己的网盘，并生成新的分享链接
- 免授权，安装即用，不需要注册码

作者：喝碗热粥　

---

## 功能

### 采集
- 可视化配置采集任务（列表页 + 详情页 XPath）
- 支持内容 2/3/4/5 分段拼接（比如正文 + 下载区 + 简介）
- 定时采集、队列执行、失败重试
- 完整的抓取日志（`logs/logs.txt`）

### 网盘转存
| 网盘 | 需要的凭据 | 转存目录 |
|---|---|---|
| 夸克网盘 | Cookie | 文件夹 fid |
| UC 网盘 | Cookie | 文件夹 fid |
| 百度网盘 | Cookie | 路径，如 `/采集转存` |
| 迅雷云盘 | refresh_token | 文件夹 id |
| 阿里云盘 | refresh_token | file_id，默认 `root` |

- 同一个链接在缓存期内只转存一次，不重复占用网盘空间
- 后台每个网盘都有「测试转存」按钮，失败会显示具体原因
- 前台按钮点击后才发起转存，访问速度不受影响

---

## 环境要求

- WordPress 5.0+
- PHP 7.0 ~ 8.3，需要 `curl`、`openssl`、`zip` 扩展
- 服务器能访问五个网盘的接口域名

---

## 安装

### 方式一：后台上传（推荐）

1. 下载本仓库的 zip（或 Releases 里的 `ol_scrapes.zip`）
2. 后台 → 插件 → 安装插件 → 上传插件 → 选择 zip → 安装 → 启用
3. 左侧会出现 **Scrapes** 菜单

### 方式二：手动上传

把本仓库所有文件放到 `wp-content/plugins/ol_scrapes/`，后台启用即可。

### 方式三：git clone

```bash
cd wp-content/plugins
git clone https://github.com/hwrz444/wordpress-collect-pan.git ol_scrapes
```

---

## 使用

### 1. 采集

后台 → Scrapes → 新建采集任务 → 填目标站点、列表页规则、详情页 XPath → 保存并启动。

### 2. 网盘转存

后台 → Scrapes → **网盘转存**，每个网盘一张卡片：

1. 填 Cookie / refresh_token（卡片里有分步获取说明）
2. 选转存目录（点「读取目录」直接选）
3. 点「测试转存」验证是否可用

### 3. 前台效果

文章正文里的网盘链接会变成按钮：

```
https://pan.quark.cn/s/xxxx        →  📦 夸克网盘
https://pan.baidu.com/s/xxxx?pwd=  →  📦 百度网盘
```

访客点击 → 插件用你的账号转存 → 返回新的分享链接。

---

## Cookie / Token 获取速查

| 网盘 | 位置 |
|---|---|
| 夸克 | pan.quark.cn 登录 → F12 → Network → 任意 `drive-pc.quark.cn` 请求 → 请求头 `Cookie` 整行 |
| UC | drive.uc.cn 登录 → F12 → Network → 任意 `pc-api.uc.cn` 请求 → 请求头 `Cookie` 整行 |
| 百度 | pan.baidu.com 登录 → F12 → Network → 请求头 `Cookie` 整行（必须含 `BDUSS`、`STOKEN`） |
| 迅雷 | pan.xunlei.com 登录 → F12 → Application → Local Storage → `credentials_` 开头那条 → JSON 里 `refresh_token`（`al.` 开头） |
| 阿里 | alipan.com 登录 → F12 → Application → Local Storage → `token` 里的 `refresh_token` |

> 详细图文步骤见插件后台「网盘转存」页面。

---

## 目录结构

```
ol_scrapes.php            插件入口
classes/                  采集核心
pan/                      网盘转存模块
  ├── bootstrap.php       引导 + 配置
  ├── loader.php          核心文件加载器
  ├── enc/                加密后的核心文件
  ├── pan/                五个网盘的实现（发布版已加密）
  ├── views/              后台设置页
  └── assets/             前端资源
views/                    采集插件的页面
languages/                语言包
libraries/                第三方库（Bootstrap / Angular）
```

---

## 常见问题

**Q：前台没出现按钮？**
检查该网盘是否填了 Cookie、是否勾选「启用」，以及文章里的链接是否是 `/s/` 分享链接。

**Q：测试转存报「未登录」？**
Cookie 复制不全或已过期，重新复制一次。

**Q：迅雷一直失败？**
确认填的是 `refresh_token`（`al.` 开头），不是 `access_token`。

**Q：要清理采集日志？**
后台「网盘转存」页底部有「清空采集日志」；也可以直接清空 `logs/logs.txt`。

---

## 免责声明

本插件仅供学习与技术研究使用。采集、转存的内容版权归原作者所有，请遵守目标网站的服务条款与当地法律法规。使用本插件产生的一切后果由使用者自行承担。

---

## 更新日志

### 5.0.0
- 采集 + 五网盘转存整合为单一插件
- 新增网盘转存后台（Cookie 配置、目录选择、测试转存）
- 修复 PHP 8 下 `Undefined variable $ol_author` 警告
- 移除授权校验，安装即用
